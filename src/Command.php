<?php

declare(strict_types=1);

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
use Orbitale\Component\ImageMagick\ReferenceClasses\Gravity;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @author Alexandre Rock Ancelet <alex@orbitale.io>
 */
class Command
{
    /**
     * The list of allowed ImageMagick binaries.
     */
    public const ALLOWED_EXECUTABLES = [
        'animate',
        'compare',
        'composite',
        'conjure',
        'convert',
        'display',
        'identify',
        'import',
        'mogrify',
        'montage',
        'stream',
    ];

    /**
     * @var string
     */
    protected $magickBinaryPath = '';

    /**
     * Environment variables used during the execution of this command.
     *
     * @var string[]
     */
    protected $env = [];

    /**
     * @var References
     */
    protected $ref;

    /**
     * The shell command.
     *
     * @var string[]
     */
    protected $command = [];

    /**
     * Parameters to add at the end of the command.
     *
     * @var string[]
     */
    protected $commandToAppend = [];

    /**
     * @var int
     */
    protected $version;

    public function __construct(?string $magickBinaryPath = '')
    {
        $magickBinaryPath = self::findMagickBinaryPath($magickBinaryPath);

        $process = Process::fromShellCommandline($magickBinaryPath.' -version');

        try {
            $code = $process->run();
        } catch (\Throwable $e) {
            throw new MagickBinaryNotFoundException('Could not check ImageMagick version', 1, $e);
        }

        if (0 !== $code || !$process->isSuccessful()) {
            throw new MagickBinaryNotFoundException(\sprintf("ImageMagick does not seem to work well, the test command resulted in an error.\n"."Execution returned message: \"{$process->getExitCodeText()}\"\n"."To solve this issue, please run this command and check your error messages to see if ImageMagick was correctly installed:\n%s", $magickBinaryPath.' -version'));
        }

        $this->ref = new References();

        $this->magickBinaryPath = $magickBinaryPath;
    }

    public static function create(?string $magickBinaryPath = null): self
    {
        return new self($magickBinaryPath);
    }

    public static function findMagickBinaryPath(?string $magickBinaryPath): string
    {
        // Delete trimming directory separator
        $magickBinaryPath = self::cleanPath((string) $magickBinaryPath, true);

        if (!$magickBinaryPath) {
            $magickBinaryPath = (new ExecutableFinder())->find('magick');
        }

        if (!$magickBinaryPath) {
            throw new MagickBinaryNotFoundException((string) $magickBinaryPath);
        }

        return $magickBinaryPath;
    }

    private static function cleanPath(string $path, bool $rtrim = false): string
    {
        $path = \str_replace('\\', '/', $path);

        if ($rtrim) {
            $path = \rtrim($path, '/');
        }

        return $path;
    }

    /**
     * This command is used for the "legacy" binaries that can still be used with ImageMagick 7.
     *
     * @see https://imagemagick.org/script/command-line-tools.php
     */
    public function getExecutable(?string $binary = null): array
    {
        if (!\in_array($binary, static::ALLOWED_EXECUTABLES, true)) {
            throw new \InvalidArgumentException(\sprintf("The ImageMagick executable \"%s\" is not allowed.\n"."The only binaries allowed to be executed are the following:\n%s", $binary, \implode(', ', static::ALLOWED_EXECUTABLES)));
        }

        return [$this->magickBinaryPath, $binary];
    }

    /**
     * Entirely reset all current command statements, and start a whole new command.
     */
    public function newCommand(?string $binary = null): self
    {
        $this->env = [];
        $this->command = $binary ? $this->getExecutable($binary) : [];
        $this->commandToAppend = [];

        return $this;
    }

    /**
     * @see https://imagemagick.org/script/convert.php
     */
    public function convert($sourceFiles, bool $checkIfFileExists = true): self
    {
        if (!\is_array($sourceFiles)) {
            $sourceFiles = [$sourceFiles];
        }

        $this->newCommand('convert');
        foreach ($sourceFiles as $sourceFile) {
            $this->file($sourceFile, $checkIfFileExists);
        }

        return $this;
    }

    /**
     * @see https://imagemagick.org/script/mogrify.php
     */
    public function mogrify(?string $sourceFile = null): self
    {
        $this->newCommand('mogrify');
        if ($sourceFile) {
            $this->file($sourceFile, true, true);
        }

        return $this;
    }

    /**
     * @see https://imagemagick.org/script/identify.php
     */
    public function identify(string $sourceFile): self
    {
        return $this->newCommand('identify')->file($sourceFile);
    }

    /**
     * @see https://imagemagick.org/script/composite.php
     */
    public function composite(): self
    {
        return $this->newCommand('composite');
    }

    /**
     * @see https://imagemagick.org/script/animate.php
     */
    public function animate(): self
    {
        return $this->newCommand('animate');
    }

    /**
     * @see https://imagemagick.org/script/compare.php
     */
    public function compare(): self
    {
        return $this->newCommand('compare');
    }

    /**
     * @see https://imagemagick.org/script/conjure.php
     */
    public function conjure(): self
    {
        return $this->newCommand('conjure');
    }

    /**
     * @see https://imagemagick.org/script/display.php
     */
    public function display(): self
    {
        return $this->newCommand('display');
    }

    /**
     * @see https://imagemagick.org/script/import.php
     */
    public function import(): self
    {
        return $this->newCommand('import');
    }

    /**
     * @see https://imagemagick.org/script/montage.php
     */
    public function montage(): self
    {
        return $this->newCommand('montage');
    }

    /**
     * @see https://imagemagick.org/script/stream.php
     */
    public function stream(): self
    {
        return $this->newCommand('stream');
    }

    public function run(): CommandResponse
    {
        $process = Process::fromShellCommandline($this->getCommand(), null, $this->env);

        $output = '';
        $error = '';

        $code = $process->run(static function ($type, $buffer) use (&$output, &$error): void {
            if (Process::ERR === $type) {
                $error .= $buffer;
            } else {
                $output .= $buffer;
            }
        });

        if ($code !== 0) {
            echo "\n>>> Command-line: « {$process->getCommandLine()} » \n";
            echo "\n>>> Output:\n>{$output}\n";
            echo "\n>>> Error:\n>{$error}\n";
        }


        return new CommandResponse($process, $code, $output, $error);
    }

    /**
     * Adds environment variable to ImageMagick at runtime.
     *
     * @see https://imagemagick.org/script/resources.php#environment
     */
    public function setEnv(string $key, string $value): void
    {
        $this->env[$key] = $value;
    }

    /**
     * Get the final command that will be executed when using Command::run().
     */
    public function getCommand(): string
    {
        return \implode(' ', \array_merge($this->command, $this->commandToAppend));
    }

    /**
     * Add a file specification to the command, mostly for source or destination file.
     */
    public function file(string $source, bool $checkIfFileExists = true, bool $appendToCommand = false): self
    {
        $source = $checkIfFileExists ? $this->checkExistingFile($source) : self::cleanPath($source);
        $source = \str_replace('\\', '/', $source);
        if ($appendToCommand) {
            $this->commandToAppend[] = $source;
        } else {
            $this->command[] = $source;
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

    /* --------------------------------- *
     * Start imagemagick native options. *
     * --------------------------------- */

    /**
     * @see http://imagemagick.org/script/command-line-options.php#background
     */
    public function background(string $color): self
    {
        $this->command[] = '-background';
        $this->command[] = '"'.$this->ref->color($color).'"';

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#fill
     */
    public function fill(string $color): self
    {
        $this->command[] = '-fill';
        $this->command[] = '"'.$this->ref->color($color).'"';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @see http://imagemagick.org/script/command-line-options.php#resize
     */
    public function resize($geometry): self
    {
        $this->command[] = '-resize';
        $this->command[] = '"'.$this->ref->geometry($geometry).'"';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @see http://imagemagick.org/script/command-line-options.php#size
     */
    public function size($geometry): self
    {
        $this->command[] = '-size';
        $this->command[] = '"'.$this->ref->geometry($geometry).'"';

        return $this;
    }

    /**
     * Create a colored canvas.
     *
     * @see http://www.imagemagick.org/Usage/canvas/
     */
    public function xc(string $canvasColor = 'none'): self
    {
        $this->command[] = '"xc:'.$this->ref->color($canvasColor).'"';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @see http://imagemagick.org/script/command-line-options.php#crop
     */
    public function crop($geometry): self
    {
        $this->command[] = '-crop';
        $this->command[] = '"'.$this->ref->geometry($geometry).'"';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @see http://imagemagick.org/script/command-line-options.php#geometry
     */
    public function geometry($geometry): self
    {
        $this->command[] = '-geometry';
        $this->command[] = '"'.$this->ref->geometry($geometry).'"';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @see http://imagemagick.org/script/command-line-options.php#extent
     */
    public function extent($geometry): self
    {
        $this->command[] = '-extent';
        $this->command[] = '"'.$this->ref->geometry($geometry).'"';

        return $this;
    }

    /**
     * @param string|Gravity $gravity
     *
     * @see https://www.imagemagick.org/script/command-line-options.php#gravity
     */
    public function gravity($gravity): self
    {
        $this->command[] = '-gravity';
        $this->command[] = '"'.$this->ref->gravity($gravity).'"';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @see http://imagemagick.org/script/command-line-options.php#thumbnail
     */
    public function thumbnail($geometry): self
    {
        $this->command[] = '-thumbnail';
        $this->command[] = '"'.$this->ref->geometry($geometry).'"';

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#quality
     */
    public function quality(int $quality): self
    {
        $this->command[] = '-quality';
        $this->command[] = (string) $quality;

        return $this;
    }

    /**
     * @param string|Geometry $page
     *
     * @see http://imagemagick.org/script/command-line-options.php#page
     */
    public function page($page): self
    {
        $this->command[] = '-page';
        $this->command[] = '"'.$this->ref->page($page).'"';

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#rotate
     */
    public function rotate(string $rotation): self
    {
        $this->command[] = '-rotate';
        $this->command[] = \escapeshellarg($this->ref->rotation($rotation));

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#strip
     */
    public function strip(): self
    {
        $this->command[] = '-strip';

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#monochrome
     */
    public function monochrome(): self
    {
        $this->command[] = '-monochrome';

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#interlace
     */
    public function interlace(string $type): self
    {
        $this->command[] = '-interlace';
        $this->command[] = $this->ref->interlace($type);

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#gaussian-blur
     */
    public function gaussianBlur($blur): self
    {
        $this->command[] = '-gaussian-blur';
        $this->command[] = $this->ref->blur($blur);

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#blur
     */
    public function blur($blur): self
    {
        $this->command[] = '-blur';
        $this->command[] = $this->ref->blur($blur);

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#font
     */
    public function font(string $fontFile, bool $checkFontFileExists = false): self
    {
        $this->command[] = '-font';
        $this->command[] = ($checkFontFileExists ? $this->checkExistingFile($fontFile) : $fontFile);

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#pointsize
     */
    public function pointsize(int $pointsize): self
    {
        $this->command[] = '-pointsize';
        $this->command[] = $pointsize;

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#stroke
     */
    public function stroke(string $color): self
    {
        $this->command[] = '-stroke';
        $this->command[] = '"'.$this->ref->color($color).'"';

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#strokewidth
     */
    public function strokeWidth(float $strokeWidth): self
    {
        $this->command[] = '-strokewidth';
        $this->command[] = (string) $strokeWidth;

        return $this;
    }

    /**
     * @see https://imagemagick.org/script/command-line-options.php#auto-orient
     */
    public function autoOrient(): self
    {
        $this->command[] = '-auto-orient';

        return $this;
    }

    /**
     * @see https://imagemagick.org/script/command-line-options.php#depth
     */
    public function depth(int $depth): self
    {
        $this->command[] = '-depth '.$depth;

        return $this;
    }

    /**
     * @see https://imagemagick.org/script/command-line-options.php#flatten
     */
    public function flatten(): self
    {
        $this->command[] = '-flatten';

        return $this;
    }

    /**
     * @see https://imagemagick.org/script/command-line-options.php#colorspace
     */
    public function colorspace(string $colorspace): self
    {
        $this->command[] = '-colorspace';
        $this->command[] = $this->ref->colorspace($colorspace);

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#transpose
     */
    public function transpose(): self
    {
        $this->command[] = '-transpose';

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#transverse
     */
    public function transverse(): self
    {
        $this->command[] = '-transverse';

        return $this;
    }

    /**
     * @see http://www.imagemagick.org/script/command-line-options.php#threshold
     */
    public function threshold(string $threshold): self
    {
        $this->command[] = '-threshold';
        $this->command[] = $this->ref->threshold($threshold);

        return $this;
    }

    /**
     * /!\ Append a raw command to ImageMagick.
     * Not safe! Use at your own risks!
     *
     * @internal
     */
    public function rawCommand(string $command, bool $append = false): self
    {
        $msg = <<<'MSG'
This command is not safe and therefore should not be used, unless you need to use an option that is not supported yet.
Use at your own risk!
If you are certain of what you are doing, you can silence this error using the "@" sign on the instruction that executes this method.
If the option you need is not supported, please open an issue or a pull-request at https://github.com/Orbitale/ImageMagickPHP in order for us to implement the option you need! 😃
MSG
;
        @\trigger_error($msg, \E_STRICT);

        if ($append) {
            $this->commandToAppend[] = $command;
        } else {
            $this->command[] = $command;
        }

        return $this;
    }

    /* ------------------------------------------ *
     * End of ImageMagick native options.         *
     * Want more? Some options are missing?       *
     * PRs are welcomed ;)                        *
     * https://github.com/Orbitale/ImageMagickPHP *
     * ------------------------------------------ */

    /* ------------------------------------------------------ *
     * Here are some nice aliases you might be interested in. *
     * They use some combinations of ImageMagick options to   *
     * ease certain common habits.                            *
     * Feel free to contribute if you have cool aliases!      *
     * ------------------------------------------------------ */

    /**
     * @see http://imagemagick.org/script/command-line-options.php#annotate
     */
    public function text(array $options = []): self
    {
        foreach (['text', 'geometry', 'textSize'] as $key) {
            if (!isset($options[$key])) {
                throw new \InvalidArgumentException(\sprintf('Key "%s" is missing for the %s function.', $key, __METHOD__));
            }
        }

        $text = $options['text'];
        $textSize = $options['textSize'];
        $geometry = $options['geometry'];
        $font = $options['font'] ?? null;
        $checkFont = $options['checkFont'] ?? false;
        $textColor = $options['textColor'] ?? null;
        $strokeWidth = $options['strokeWidth'] ?? null;
        $strokeColor = $options['strokeColor'] ?? 'none';

        if ($font) {
            $this->font($font, $checkFont);
        }

        $this->pointsize($textSize);

        if ($textColor) {
            $this->fill($textColor);
        }

        if (null !== $strokeWidth) {
            $this->strokeWidth($strokeWidth);
        }
        $this->stroke($strokeColor);

        $this->command[] = '-annotate';
        $this->command[] = '"'.$this->ref->geometry($geometry).'"';
        $this->command[] = \escapeshellarg($text);

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#draw
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
            $this->stroke($strokeColor);
        }

        $this->fill($fillColor);

        $this->command[] = '-draw';
        $this->command[] = '"ellipse '.$xCenter.','.$yCenter.$width.','.$height.$startAngleInDegree.','.$endAngleInDegree.'"';

        return $this;
    }

    /**
     * @see http://imagemagick.org/script/command-line-options.php#draw
     */
    public function polyline(array $coordinates, string $strokeColor = ''): self
    {
        if ($strokeColor) {
            $this->stroke($strokeColor);
        }

        $this->fill('transparent');

        $this->command[] = '-draw';
        $this->command[] = '"polyline '.\implode(' ', $coordinates).'"';

        return $this;
    }

    protected function checkExistingFile(string $file): string
    {
        if (!\file_exists($file)) {
            throw new \InvalidArgumentException(\sprintf('The file "%s" is not found.'."\n".'If the file really exists in your filesystem, then maybe it is not readable.', $file));
        }

        return self::cleanPath($file);
    }
}
