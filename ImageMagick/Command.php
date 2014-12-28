<?php
/*
* This file is part of the PierstovalImageMagickPHP package.
*
* (c) Alexandre "Pierstoval" Rock Ancelet <pierstoval@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Pierstoval\Component\ImageMagick;

/**
 * Class Command
 * Project corahn_rin
 *
 * @author Alexandre "Pierstoval" Ancelet <pierstoval@gmail.com>
 */
class Command
{

    /**
     * @var array The list of allowed ImageMagick binaries
     */
    protected $allowedExecutables = array('convert', 'mogrify', 'identify');

    /**
     * @var References
     */
    protected $ref;

    protected $imageMagickPath = '';

    protected $command = '';
    protected $commandToAppend = '';

    public function __construct($imageMagickPath = '/usr/bin')
    {
        // We must use "exec" for this command, because we don't rely on the ImageMagick PHP extension.
        // To execute the ImageMagick binaries, then, we check the availability of "exec"
        if (
            in_array('exec', explode(',', ini_get('disable_functions')))
            || !function_exists('exec')
            || (strtolower(ini_get('safe_mode')) === 'off')
        ) {
            throw new \RuntimeException('The "exec" function must be available to use ImageMagick commands.');
        }

        // Get realpath, delete trimming directory separator
        $imageMagickPath = preg_replace('~[\\\/]$~', '', $imageMagickPath);
        if ($imageMagickPath) {
            // Add a proper "/" at the end if path is not empty.
            // If it's empty, then it's set in the global path.
            $imageMagickPath .= '/';
            if (!is_dir($imageMagickPath)) {
                throw new \InvalidArgumentException(sprintf(
                    "The specified path (%s) is not a directory.\n" .
                    "You must set the \"imageMagickPath\" parameter as the root directory where\n" .
                    "ImageMagick executables (%s) are located.",
                    $imageMagickPath,
                    implode(', ', $this->allowedExecutables)
                ));
            }
        }

        exec($imageMagickPath . 'convert -version', $o, $code);
        if ($code !== 0) {
            throw new \InvalidArgumentException(sprintf(
                "The specified path (%s) does not seem to contain ImageMagick binaries, or it is not readable.\n" .
                "If ImageMagick is set in the path, then set an empty parameter for `imageMagickPath`.\n",
                "If not, then set the absolute path of the directory containing ImageMagick following executables:\n%s",
                $imageMagickPath,
                implode(', ', $this->allowedExecutables)
            ));
        }

        $this->ref = new References;

        $this->imageMagickPath = $imageMagickPath;
    }

    /**
     * Executes the command and returns its response
     * @return CommandResponse
     */
    public function run()
    {
        exec($this->command . ' ' . $this->commandToAppend, $output, $code);
        return new CommandResponse($output, $code);
    }

    /**
     * Start a new command with the "convert" executable (if allowed)
     * @param $source
     * @return $this
     */
    public function convert($source)
    {
        return $this->newCommand('convert')->file($source);
    }

    /**
     * Start a new command with the "mogrify" executable (if allowed)
     * @param $source
     * @return $this
     */
    public function mogrify($source)
    {
        return $this->newCommand('mogrify')->file($source);
    }

    /**
     * Start a new command with the "identify" executable (if allowed)
     * @param $source
     * @return $this
     */
    public function identify($source)
    {
        return $this->newCommand('identify')->file($source);
    }

    public function getCommand()
    {
        return $this->command.' '.$this->commandToAppend;
    }

    public function resize($geometry)
    {
        $this->command .= ' -resize '.$this->ref->geometry($geometry).' ';
        return $this;
    }

    /**
     * Adds text to the currently converted element.
     * @param string $text The text to add
     * @param mixed $geometry Text position in the picture or pdf. This must fit ImageMagick geometry reference
     * @param integer $size The text size, in points unit.
     * @param string $color A color for your text. This must fit ImageMagick color reference
     * @param string $font
     * @return $this
     */
    public function text($text, $geometry, $size, $color = 'black', $font = null)
    {
        $this->command .=
            ($font ? ' -font "' . $this->checkExistingFile($font) . '"' : '') .
            ' -pointsize ' . (int)$size .
            ($color ? ' -fill "' . $this->ref->color($color) . '"' : '') .
            ' -stroke "none"' .
            ' -annotate ' . $this->ref->geometry($geometry) . ' ' . $this->escape($text) . ' ';

        return $this;
    }

    /**
     * Creates an ellipse (or a circle, depending of the settings) to place in the picture or pdf
     * @param integer $xCenter The "x" coordinate to the center of the ellipse
     * @param integer $yCenter The "y" coordinate to the center of the ellipse
     * @param integer $width The ellipse width
     * @param integer $height The ellipse height
     * @param string $fill_color A color to fill. This must fit ImageMagick color reference
     * @param string $stroke_color A color for the stroke outline. This must fit ImageMagick color reference
     * @param int $angle_start The start angle position, in degrees
     * @param int $angle_end The end angle position, in degrees
     * @return $this
     */
    public function ellipse($xCenter, $yCenter, $width, $height, $fill_color, $stroke_color = '', $angle_start = 0, $angle_end = 360
    ) {
        if ($stroke_color) {
            $this->command .= ' -stroke "' . $this->ref->color($stroke_color) . '"';
        }
        $this->command .=
            ' -fill "' . $this->ref->color($fill_color) . '"' .
            ' -draw "ellipse ' . (int)$xCenter . ',' . (int)$yCenter .
            ' ' . (int)$width . ',' . (int)$height .
            ' ' . (int)$angle_start . ',' . (int)$angle_end . '"';
        return $this;
    }

    /**
     * @param string $binary One of the allowed ImageMagick executables
     * @return string
     */
    public function getExecutable($binary = 'convert')
    {
        if (!in_array($binary, $this->allowedExecutables)) {
            throw new \InvalidArgumentException(sprintf(
                "The ImageMagick executable \"%s\" is not allowed.\n" .
                "The only binaries allowed to be executed are the following:\n%s",
                $binary,
                implode(', ', $this->allowedExecutables)
            ));
        }

        return $this->imageMagickPath . $binary;
    }

    /**
     * Start a whole new command
     * @param string $executable An allowed ImageMagick executable
     * @return $this
     */
    public function newCommand($executable)
    {
        $this->command = ' "' . $this->getExecutable($executable) . '" ';
        $this->commandToAppend = '';
        return $this;
    }

    /**
     * Add a file specification, mostly for source or destination file
     * @param string $source The file must exists
     * @param bool $checkExistence If true, checks file existence before using it
     * @return $this
     */
    public function file($source, $checkExistence = true)
    {
        $source = $checkExistence ? $this->checkExistingFile($source) : $source;
        $source = str_replace('\\', '/', $source);
        $this->command .= ' "' . $source . '" ';
        return $this;
    }

    /**
     * Checks if file exists in the filesystem
     * @param string $file
     * @return string
     */
    protected function checkExistingFile($file)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf(
                "The file \"%s\" is not found.\n" .
                "If the file really exists in your filesystem, then maybe it is not readable.",
                $file
            ));
        }
        return $file;
    }

    /**
     * Escapes a string in order to inject it in the shell command
     * @param string $string
     * @param bool $addQuotes
     * @return mixed|string
     */
    protected function escape($string, $addQuotes = true)
    {
        $string = str_replace(
            array('"', '`', '’', '\\\''),
            array('\"', "'", "'", "'"),
            trim($string)
        );
        return $addQuotes ? '"' . $string . '"' : $string;
    }
}
