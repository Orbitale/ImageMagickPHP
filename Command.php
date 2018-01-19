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

    public function setEnv(string $key, string $value)
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

    public function getCommand(): string
    {
        return $this->env.' '.$this->command.' '.$this->commandToAppend;
    }

    /**
     * Adds text to the currently converted element.
     *
     * @param string|Geometry $geometry
     */
    public function text(string $text, $geometry, int $textSize, string $textColor = 'black', string $font = null): self
    {
        $this->command .=
            ($font ? ' -font '.$this->escape($this->checkExistingFile($font)) : '').
            ' -pointsize '.$textSize.
            ($textColor ? ' -fill '.$this->escape($this->ref->color($textColor)) : '').
            ' -stroke "none"'.
            ' -annotate '.$this->ref->geometry($geometry).' '.$this->escape($text)
        ;

        return $this;
    }

    /**
     * Creates an ellipse (or a circle, depending of the settings) to place in the previously selected source file.
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
    ): self {
        if ($strokeColor) {
            $this->command .= ' -stroke "'.$this->ref->color($strokeColor).'"';
        }
        $this->command .=
            ' -fill "'.$this->ref->color($fillColor).'"'.
            ' -draw "ellipse '.$xCenter.','.$yCenter.
            ' '.$width.','.$height.
            ' '.$startAngleInDegree.','.$endAngleInDegree.'"';

        return $this;
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
     * Start a whole new command.
     */
    public function newCommand(string $executable): self
    {
        $this->command         = ''.$this->getExecutable($executable).'';
        $this->commandToAppend = '';

        return $this;
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
