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

/**
 * Here lies the logic behind all supported ImageMagick options.
 *
 * @link http://www.imagemagick.org/script/command-line-options.php
 */
abstract class CommandOptions
{

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
     * Escapes a string in order to inject it in the shell command
     *
     * @param string $string
     * @param bool   $addQuotes
     *
     * @return mixed|string
     */
    public function escape($string, $addQuotes = true)
    {
        $string = str_replace(
            array('"', '`', '’', '\\\''),
            array('\"', "'", "'", "'"),
            trim($string)
        );

        return $addQuotes ? '"'.$string.'"' : $string;
    }

    //
    // Now, the options.
    //

    /**
     * @param string $color
     *
     * @return $this
     */
    public function background($color)
    {
        $this->command .= ' -background ' . $this->ref->color($color);

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @return $this
     */
    public function resize($geometry)
    {
        $this->command .= ' -resize ' . $this->escape($this->ref->geometry($geometry)).' ';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @return $this
     */
    public function crop($geometry)
    {
        $this->command .= ' -crop ' . $this->escape($this->ref->geometry($geometry)).' ';

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @return $this
     */
    public function extent($geometry)
    {
        $this->command .= ' -extent ' . $this->escape($this->ref->geometry($geometry));

        return $this;
    }

    /**
     * @param string|Geometry $geometry
     *
     * @return $this
     */
    public function thumbnail($geometry)
    {
        $this->command .= ' -thumbnail ' . $this->escape($this->ref->geometry($geometry));

        return $this;
    }

    /**
     * @param int $quality
     *
     * @return $this
     */
    public function quality($quality)
    {
        $this->command .= ' -quality ' . ((int)$quality);

        return $this;
    }

    /**
     * @param string $rotation
     *
     * @return $this
     */
    public function rotate($rotation)
    {
        $this->command .= ' -rotate ' . $this->escape($this->ref->rotation($rotation));

        return $this;
    }

    /**
     * @return $this
     */
    public function strip()
    {
        $this->command .= ' -strip ';

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function interlace($type)
    {
        $this->command .= ' -interlace ' . $type;

        return $this;
    }

    /**
     * @param Float $radius
     * @return $this
     */
    public function gaussianBlur($radius)
    {
        $this->command .= ' -gaussian-blur ' . ((float)$radius);

        return $this;
    }

}
