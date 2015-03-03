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


use Symfony\Component\Yaml\Yaml;

final class References
{

    /**
     * @var array Configuration from the references.yml file
     */
    private $config = array();

    public function __construct($referenceFile = null)
    {
        if (null === $referenceFile) {
            $referenceFile = __DIR__.'/Resources/references.yml';
        }
        if (!file_exists($referenceFile)) {
            throw new \RuntimeException(sprintf(
                "File %s for ImageMagick references does not exist.\n".
                "Check that the file exists and that it is readable.",
                $referenceFile
            ));
        }
        $config = Yaml::parse(file_get_contents($referenceFile));
        if (is_array($config) && count($config)) {
            $this->config = $config;
        } else {
            throw new \InvalidArgumentException(sprintf(
                "File %s for ImageMagick references seems to be empty.\n".
                "If it is a YAML file, please check its contents.",
                $referenceFile
            ));
        }
    }

    /**
     * @link http://www.imagemagick.org/script/command-line-processing.php#geometry
     *
     * @param mixed $geometry
     *
     * @return string
     */
    public function geometry($geometry)
    {
        $geometry = trim($geometry);
        if (
            preg_match('~^(?:x?[0-9]+(?:\.[0-9]+)?%?|[0-9]+(?:\.[0-9]+)?%?x[0-9]+(?:\.[0-9]+)?%?[\^!<>]?)?(?:[+-][0-9]+[+-][0-9]+)?$~', $geometry)
        ) {
            return $geometry;
        } else {
            throw new \InvalidArgumentException(sprintf(
                "The specified geometry (%s) is invalid.\n".
                "Please refer to ImageMagick command line documentation about geometry:\n%s",
                $geometry,
                'http://www.imagemagick.org/script/command-line-processing.php#geometry'
            ));
        }
    }

    /**
     * Checks that a color is correct according to ImageMagick command line reference
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
            preg_match('~^#(?:[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{12})$~i', $color) // Check hex
            || preg_match('~^#([a-f0-9]{8}|[a-f0-9]{16})$~i', $color) // Check hexa
            || preg_match('~^rgb\([0-9]{1,3}(\.[0-9]{1,2})?%?, ?[0-9]{1,3}(\.[0-9]{1,2})?%?, ?[0-9]{1,3}(\.[0-9]{1,2})?%?\)$~', $color) // Check rgb
            || preg_match('~^rgba\([0-9]{1,3}(\.[0-9]{1,2})?%?, ?[0-9]{1,3}(\.[0-9]{1,2})?%?, ?[0-9]{1,3}(\.[0-9]{1,2})?%?, ?[01](\.[0-9]{1,6})?\)$~', $color) // Check rgba
            || in_array($color, $this->config['colors'])// And check the dirty one : all the color names supported by ImageMagick
        ) {
            return $color;
        } else {
            throw new \InvalidArgumentException(sprintf(
                "The specified color (%s) is invalid.\n".
                "Please refer to ImageMagick command line documentation about colors:\n%s",
                $color,
                'http://www.imagemagick.org/script/color.php'
            ));
        }
    }
}
