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
use Symfony\Component\Yaml\Yaml;

/**
 * This class is here to add some validation processes when using options in the Command class.
 */
final class References
{

    /**
     * @var array Configuration from the references.yml file
     */
    private $config = array();

    public function __construct()
    {
        $referenceFile = __DIR__.'/Resources/references.yml';

        if (!file_exists($referenceFile)) {
            throw new \RuntimeException(sprintf(
                'File %s for ImageMagick references does not exist.'."\n".
                'Check that the file exists and that it is readable.',
                $referenceFile
            ));
        }
        $config = Yaml::parse(file_get_contents($referenceFile));
        if (is_array($config) && count($config)) {
            $this->config = $config;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'File %s for ImageMagick references seems to be empty.'."\n".
                'If it is a YAML file, please check its contents.',
                $referenceFile
            ));
        }
    }

    /**
     * Checks that a geometry option is correct according to ImageMagick Geometry reference.
     * @link http://www.imagemagick.org/script/command-line-processing.php#geometry
     *
     * @param string|Geometry $geometry
     *
     * @return string
     */
    public function geometry($geometry)
    {
        if (!$geometry instanceof Geometry) {
            $geometry = new Geometry(trim($geometry));
        }

        return $geometry->validate();
    }

    /**
     * Checks that a color is correct according to ImageMagick command line reference.
     * @link http://www.imagemagick.org/script/color.php
     *
     * @param $color
     *
     * @return string
     */
    public function color($color)
    {
        $color = trim($color);
        if (
            // Check "hex"
            preg_match('~^#(?:[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{12})$~i', $color)

            // Check "hexa"
            || preg_match('~^#([a-f0-9]{8}|[a-f0-9]{16})$~i', $color)
            || preg_match('~^rgb\(\d{1,3}(\.\d{1,2})?%?, ?\d{1,3}(\.\d{1,2})?%?, ?\d{1,3}(\.\d{1,2})?%?\)$~', $color)

            // Check "rgb"
            || preg_match('~^rgba\(\d{1,3}(\.\d{1,2})?%?, ?\d{1,3}(\.\d{1,2})?%?, ?\d{1,3}(\.\d{1,2})?%?, ?[01](\.\d{1,6})?\)$~', $color)

            // Check "rgba"
            || in_array($color, $this->config['colors'], true)// And check the dirty one : all the color names supported by ImageMagick
        ) {
            return $color;
        }

        throw new \InvalidArgumentException(sprintf(
            'The specified color (%s) is invalid.'."\n".
            'Please refer to ImageMagick command line documentation about colors:'."\n%s",
            $color,
            'http://www.imagemagick.org/script/color.php'
        ));
    }

    /**
     * Checks that a rotation option is correct according to ImageMagick command line reference.
     * @link http://www.imagemagick.org/script/command-line-options.php#rotate
     *
     * @param string $rotation
     *
     * @return string
     */
    public function rotation($rotation)
    {
        if (preg_match('~^-?\d+(?:<|>)$~u', $rotation)) {
            return $rotation;
        }

        throw new \InvalidArgumentException(sprintf(
            'The specified rotate parameter (%s) is invalid.'."\n".
            'Please refer to ImageMagick command line documentation about the "-rotate" option:'."\n%s",
            $rotation,
            'http://www.imagemagick.org/script/command-line-options.php#rotate'
        ));
    }
}
