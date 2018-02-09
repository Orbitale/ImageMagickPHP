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
     * @return $this
     */
    public function background(string $color)
    {
        $this->command .= ' -background ' . $this->escape($this->ref->color($color));

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
    public function size($geometry)
    {
        $this->command .= ' -size ' . $this->escape($this->ref->geometry($geometry)).' ';

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
     * @return $this
     */
    public function quality(int $quality)
    {
        $this->command .= ' -quality ' .$quality;

        return $this;
    }

    /**
     * @return $this
     */
    public function rotate(string $rotation)
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
     * @return $this
     */
    public function interlace(string $type)
    {
        $this->command .= ' -interlace '.$this->ref->interlace($type);

        return $this;
    }

    /**
     * @return $this
     */
    public function gaussianBlur(string $blur)
    {
        $this->command .= ' -gaussian-blur '.$this->ref->blur($blur);

        return $this;
    }

    /**
     * @return $this
     */
    public function blur(string $blur)
    {
        $this->command .= ' -blur '.$this->ref->blur($blur);

        return $this;
    }

}
