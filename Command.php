<?php

/*
 * This file is part of the OrbitaleImageMagickPHP package.
 *
 * (c) Alexandre Rock Ancelet <alex@orbitale.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orbitale\Component\ImageMagick;

use Orbitale\Component\ImageMagick\ReferenceClasses\Geometry;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @author Alexandre Rock Ancelet <alex@orbitale.io>
 */
class Command extends CommandOptions
{
    public const RUN_NORMAL     = null;
    public const RUN_BACKGROUND = ' > /dev/null 2>&1';
    public const RUN_DEBUG      = ' 2>&1';

    public const VERSION_6 = 6;
    public const VERSION_7 = 7;

    /**
     * The list of allowed ImageMagick binaries
     */
    public const ALLOWED_EXECUTABLES = ['convert', 'mogrify', 'identify'];

    /**
     * @var string
     */
    protected $imageMagickPath = '';

    /**
     * Environment values
     * @var string
     */
    protected $env = '';

    /**
     * @var References
     */
    protected $ref;

    /**
     * The shell command
     * @var string
     */
    protected $command = '';

    /**
     * Parameters to add at the end of the command
     * @var string
     */
    protected $commandToAppend = '';

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var int
     */
    protected $version;

    public function __construct(string $imageMagickPath = '/usr/bin')
    {
        $this->fs = new Filesystem();

        // Delete trimming directory separator
        $imageMagickPath = $this->cleanPath($imageMagickPath, true);

        // Add a proper directory separator at the end if path is not empty.
        // If it's empty, then it's set in the global path.
        if ($imageMagickPath && !is_dir($imageMagickPath)) {
            throw new \InvalidArgumentException(sprintf(
                'The specified path (%s) is not a directory.'."\n".
                'You must set the "imageMagickPath" parameter as the root directory where'."\n".
                'ImageMagick executables (%s) are located.',
                $imageMagickPath,
                implode(', ', static::ALLOWED_EXECUTABLES)
            ));
        }

        // For imagemagick 7 we'll use the "magick" base command each time.
        if ($this->fs->exists($imageMagickPath.'/magick') || $this->fs->exists($imageMagickPath.'/magick.exe')) {
            $this->version = static::VERSION_7;
            $imageMagickPath .= '/magick ';
        } elseif ($this->fs->exists($imageMagickPath.'/convert') || $this->fs->exists($imageMagickPath.'/convert.exe')) {
            $this->version = static::VERSION_6;
            $imageMagickPath .= '/';
        } else {
            throw new \InvalidArgumentException(sprintf(
                "The specified path (%s) does not seem to contain ImageMagick binaries, or it is not readable.\n".
                "If ImageMagick is set in the path, then set an empty parameter for `imageMagickPath`.\n".
                "If not, then set the absolute path of the directory containing ImageMagick following executables:\n%s",
                $imageMagickPath,
                implode(', ', static::ALLOWED_EXECUTABLES)
            ));
        }

        $process = new Process($imageMagickPath.'convert -version 2>&1');

        $process->run();
        if (!$process->isSuccessful()) {
            throw new \InvalidArgumentException(sprintf(
                "ImageMagick does not seem to work well, the test command resulted in an error.\n".
                "Execution returned message: \"{$process->getExitCodeText()}\"\n".
                "To solve this issue, please run this command and check your error messages:\n%s",
                $imageMagickPath.'convert -version'
            ));
        }

        $this->ref = new References();

        $this->imageMagickPath = $imageMagickPath;
    }

    public static function create(string $imageMagickPath = '/usr/bin'): self
    {
        return new self($imageMagickPath);
    }

    public function getExecutable(string $binary = 'convert'): string
    {
        if (!\in_array($binary, static::ALLOWED_EXECUTABLES, true)) {
            throw new \InvalidArgumentException(sprintf(
                "The ImageMagick executable \"%s\" is not allowed.\n".
                "The only binaries allowed to be executed are the following:\n%s",
                $binary,
                implode(', ', static::ALLOWED_EXECUTABLES)
            ));
        }

        $executablePath = $this->imageMagickPath;

        if ($this->version === self::VERSION_7){
            // ImageMagick 7 uses the "magick" main command,
            //  so every binary is transformed into an argument for the magick binary.
            $executablePath .= ' ';
        }

        $executablePath .= $binary;

        return $executablePath;
    }

    /**
     * Entirely reset all current command statements, and start a whole new command.
     *
     */
    public function newCommand(string $executable): self
    {
        $this->command         = ''.$this->getExecutable($executable).'';
        $this->commandToAppend = '';

        return $this;
    }

    public function run(?string $runMode = self::RUN_NORMAL): CommandResponse
    {
        if (!\in_array($runMode, [self::RUN_NORMAL, self::RUN_BACKGROUND, self::RUN_DEBUG], true)) {
            throw new \InvalidArgumentException('The run mode must be one of '.__CLASS__.'::RUN_* constants.');
        }

        $process = new Process($this->env.' '.$this->command.' '.$this->commandToAppend.$runMode);

        $output = '';
        $error = '';

        $code = $process->run(function ($type, $buffer) use (&$output, &$error) {
            if (Process::ERR === $type) {
                $error .= $buffer;
            } else {
                $output .= $buffer;
            }
        });

        return new CommandResponse($process, $code, $output, $error);
    }

    public function setEnv(string $key, string $value): void
    {
        $this->env .= ' '.$key.'='.escapeshellarg($value);
    }

    /**
     * Start a new command with the "convert" executable (if allowed).
     */
    public function convert(string $sourceFile): self
    {
        return $this->newCommand('convert')->file($sourceFile);
    }

    /**
     * Start a new command with the "mogrify" executable (if allowed)
     */
    public function mogrify(string $sourceFile = null): self
    {
        $this->newCommand('mogrify');
        if ($sourceFile) {
            $this->file($sourceFile, true, true);
        }

        return $this;
    }

    /**
     * Start a new command with the "identify" executable (if allowed)
     */
    public function identify(string $sourceFile): self
    {
        return $this->newCommand('identify')->file($sourceFile);
    }

    /**
     * Get the final command that will be executed when using Command::run()
     */
    public function getCommand(): string
    {
        return $this->env.' '.$this->command.' '.$this->commandToAppend;
    }

    /**
     * Escapes a string in order to inject it in the shell command.
     */
    public function escape(string $string, bool $addQuotes = true): string
    {
        $string = str_replace(
            ['"', '`', '’', '\\\''],
            ['\"', "'", "'", "'"],
            trim($string)
        );

        return $addQuotes ? '"'.$string.'"' : $string;
    }

    /**
     * Add a file specification to the command, mostly for source or destination file.
     */
    public function file(string $source, bool $checkIfFileExists = true, bool $appendToCommend = false): self
    {
        $source = $checkIfFileExists ? $this->checkExistingFile($source) : $this->cleanPath($source);
        $source = str_replace('\\', '/', $source);
        if ($appendToCommend) {
            $this->commandToAppend .= ' "'.$source.'"';
        } else {
            $this->command .= ' "'.$source.'"';
        }

        return $this;
    }

    /**
     * Add an output file to the end of the command.
     */
    public function output(string $source): self
    {
        return $this->file($source, false, true);
    }

    /* ---------------------------- *
     * ---------------------------- *
     * ---------------------------- *
     *     ImageMagick Features     *
     * ---------------------------- *
     * ---------------------------- *
     * ---------------------------- */

    /**
     * @link http://imagemagick.org/script/command-line-options.php#background
     */
    public function background(string $color): self
    {
        $this->command .= ' -background ' . $this->escape($this->ref->color($color));

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#fill
     */
    public function fill(string $color): self
    {
        $this->command .= ' -fill ' . $this->escape($this->ref->color($color));

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @link http://imagemagick.org/script/command-line-options.php#resize
     */
    public function resize($geometry): self
    {
        $this->command .= ' -resize ' . $this->escape($this->ref->geometry($geometry)).' ';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @link http://imagemagick.org/script/command-line-options.php#size
     */
    public function size($geometry): self
    {
        $this->command .= ' -size ' . $this->escape($this->ref->geometry($geometry)).' ';

        return $this;
    }

    /**
     * Create a colored canvas.
     *
     * @param string $canvasColor
     *
     * @link http://www.imagemagick.org/Usage/canvas/
     */
    public function xc(string $canvasColor = 'none'): self
    {
        $this->command .= ' xc:' . $this->escape($this->ref->color($canvasColor)).' ';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @link http://imagemagick.org/script/command-line-options.php#crop
     */
    public function crop($geometry): self
    {
        $this->command .= ' -crop ' . $this->escape($this->ref->geometry($geometry), false).' ';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @link http://imagemagick.org/script/command-line-options.php#extent
     */
    public function extent($geometry): self
    {
        $this->command .= ' -extent ' . $this->escape($this->ref->geometry($geometry));

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @link http://imagemagick.org/script/command-line-options.php#thumbnail
     */
    public function thumbnail($geometry): self
    {
        $this->command .= ' -thumbnail ' . $this->escape($this->ref->geometry($geometry));

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#quality
     */
    public function quality(int $quality): self
    {
        $this->command .= ' -quality ' .$quality;

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#rotate
     */
    public function rotate(string $rotation): self
    {
        $this->command .= ' -rotate ' . $this->escape($this->ref->rotation($rotation));

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#strip
     */
    public function strip(): self
    {
        $this->command .= ' -strip ';

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#interlace
     */
    public function interlace(string $type): self
    {
        $this->command .= ' -interlace '.$this->ref->interlace($type);

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#gaussian-blur
     */
    public function gaussianBlur(string $blur): self
    {
        $this->command .= ' -gaussian-blur '.$this->ref->blur($blur);

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#blur
     */
    public function blur(string $blur): self
    {
        $this->command .= ' -blur '.$this->ref->blur($blur);

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#font
     */
    public function font(string $fontFile): self
    {
        $this->command .= ' '.$this->escape($this->checkExistingFile($fontFile)).' ';

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#pointsize
     */
    public function pointsize(int $pointsize): self
    {
        $this->command .= ' -pointsize ' .$pointsize;

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#stroke
     */
    public function stroke(int $color): self
    {
        $this->command .= ' -stroke ' .$this->ref->color($color);

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @link http://imagemagick.org/script/command-line-options.php#annotate
     */
    public function text(string $text, $geometry, int $textSize, string $textColor = 'black', string $font = null): self
    {
        if ($font) {
            $this->font($font);
        }

        $this->pointsize($textSize);

        if ($textColor) {
            $this->fill($textColor);
        }

        $this->stroke('none');

        $this->command .= ' -annotate '.$this->ref->geometry($geometry).' '.$this->escape($text);

        return $this;
    }

    /**
     * @link http://imagemagick.org/script/command-line-options.php#draw
     */
    public function ellipse(
        int $xCenter,
        int $yCenter,
        int $width,
        int $height,
        string $fillColor,
        string $strokeColor = '',
        int $startAngleInDegree = 0,
        int $endAngleInDegree = 360
    ): self
    {
        if ($strokeColor) {
            $this->stroke($strokeColor);
        }

        $this->fill($fillColor);

        $this->command .=
            ' -draw "ellipse '.$xCenter.','.$yCenter.
            ' '.$width.','.$height.
            ' '.$startAngleInDegree.','.$endAngleInDegree.'"';

        return $this;
    }

    /* ---------------------------- *
     * End of ImageMagick functions *
     * ---------------------------- */

    /**
     * Checks if file exists in the filesystem.
     */
    protected function checkExistingFile(string $file): string
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" is not found.'."\n".
                'If the file really exists in your filesystem, then maybe it is not readable.',
                $file
            ));
        }

        return $this->cleanPath($file);
    }

    private function cleanPath(string $path, bool $rtrim = false): string
    {
        $path = str_replace('\\', '/', $path);

        if ($rtrim) {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
