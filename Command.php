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
    const RUN_NORMAL     = null;
    const RUN_BACKGROUND = ' > /dev/null 2>&1';
    const RUN_DEBUG      = ' 2>&1';

    const VERSION_6 = 6;
    const VERSION_7 = 7;

    /**
     * @var array The list of allowed ImageMagick binaries
     */
    protected $allowedExecutables = array('convert', 'mogrify', 'identify');

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

    public function __construct($imageMagickPath = '/usr/bin')
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
                implode(', ', $this->allowedExecutables)
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
                implode(', ', $this->allowedExecutables)
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

    /**
     * Executes the command and returns its response
     *
     * @param null $runMode
     *
     * @return CommandResponse
     */
    public function run($runMode = self::RUN_NORMAL)
    {
        if (!in_array($runMode, array(self::RUN_NORMAL, self::RUN_BACKGROUND, self::RUN_DEBUG), true)) {
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

    public function setEnv($key, $value)
    {
        $this->env .= ' '.$key.'='.escapeshellarg($value);
    }

    /**
     * Start a new command with the "convert" executable (if allowed)
     *
     * @param $source
     *
     * @return $this
     */
    public function convert($source)
    {
        return $this->newCommand('convert')->file($source);
    }

    /**
     * Start a new command with the "mogrify" executable (if allowed)
     * @var string $source An image to mogrify, it has to exist.
     * @return $this
     */
    public function mogrify($source = null)
    {
        $this->newCommand('mogrify');
        if ($source) {
            $this->file($source, true, true);
        }

        return $this;
    }

    /**
     * Start a new command with the "identify" executable (if allowed)
     *
     * @param $source
     *
     * @return $this
     */
    public function identify($source)
    {
        return $this->newCommand('identify')->file($source);
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->env.' '.$this->command.' '.$this->commandToAppend;
    }

    /**
     * Adds text to the currently converted element.
     *
     * @param string  $text     The text to add
     * @param string|Geometry   $geometry Text position in the picture or pdf. This must fit ImageMagick geometry reference
     * @param integer $size     The text size, in points unit.
     * @param string  $color    A color for your text. This must fit ImageMagick color reference
     * @param string  $font
     *
     * @return $this
     */
    public function text($text, $geometry, $size, $color = 'black', $font = null)
    {
        $this->command .=
            ($font ? ' -font "'.$this->checkExistingFile($font).'"' : '').
            ' -pointsize '.((int) $size).
            ($color ? ' -fill "'.$this->ref->color($color).'"' : '').
            ' -stroke "none"'.
            ' -annotate '.$this->ref->geometry($geometry).' '.$this->escape($text)
        ;

        return $this;
    }

    /**
     * Creates an ellipse (or a circle, depending of the settings) to place in the picture or pdf
     *
     * @param integer $xCenter      The "x" coordinate to the center of the ellipse
     * @param integer $yCenter      The "y" coordinate to the center of the ellipse
     * @param integer $width        The ellipse width
     * @param integer $height       The ellipse height
     * @param string  $fill_color   A color to fill. This must fit ImageMagick color reference
     * @param string  $stroke_color A color for the stroke outline. This must fit ImageMagick color reference
     * @param int     $angle_start  The start angle position, in degrees
     * @param int     $angle_end    The end angle position, in degrees
     *
     * @return $this
     */
    public function ellipse(
        $xCenter,
        $yCenter,
        $width,
        $height,
        $fill_color,
        $stroke_color = '',
        $angle_start = 0,
        $angle_end = 360
    ) {
        if ($stroke_color) {
            $this->command .= ' -stroke "'.$this->ref->color($stroke_color).'"';
        }
        $this->command .=
            ' -fill "'.$this->ref->color($fill_color).'"'.
            ' -draw "ellipse '.(int) $xCenter.','.(int) $yCenter.
            ' '.(int) $width.','.(int) $height.
            ' '.(int) $angle_start.','.(int) $angle_end.'"';

        return $this;
    }

    /**
     * @param string $binary One of the allowed ImageMagick executables
     *
     * @return string
     */
    public function getExecutable($binary = 'convert')
    {
        if (!in_array($binary, $this->allowedExecutables, true)) {
            throw new \InvalidArgumentException(sprintf(
                "The ImageMagick executable \"%s\" is not allowed.\n".
                "The only binaries allowed to be executed are the following:\n%s",
                $binary,
                implode(', ', $this->allowedExecutables)
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
     * Start a whole new command
     *
     * @param string $executable An allowed ImageMagick executable
     *
     * @return $this
     */
    public function newCommand($executable)
    {
        $this->command         = ''.$this->getExecutable($executable).'';
        $this->commandToAppend = '';

        return $this;
    }

    /**
     * Add a file specification, mostly for source or destination file
     *
     * @param string $source         The file must exists
     * @param bool   $checkExistence If true, checks file existence before using it
     * @param bool   $append         If true, appends the file name instead of adding it to the command normally
     *
     * @return $this
     */
    public function file($source, $checkExistence = true, $append = false)
    {
        $source = $checkExistence ? $this->checkExistingFile($source) : $this->cleanPath($source);
        $source = str_replace('\\', '/', $source);
        if ($append) {
            $this->commandToAppend .= ' "'.$source.'"';
        } else {
            $this->command .= ' "'.$source.'"';
        }

        return $this;
    }

    /**
     * Checks if file exists in the filesystem
     *
     * @param string $file
     *
     * @return string
     */
    protected function checkExistingFile($file)
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

    /**
     * @param string $path
     * @param bool   $rtrim
     * @return string
     */
    private function cleanPath($path, $rtrim = false)
    {
        $path = str_replace('\\', '/', $path);

        if ($rtrim) {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
