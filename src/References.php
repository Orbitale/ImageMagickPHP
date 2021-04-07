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

/**
 * This class is here to add some validation processes when using options in the Command class.
 */
final class References
{
    /**
     * @var array Configuration from the references.php file
     */
    private $config = [];

    public function __construct()
    {
        $referenceFile = \dirname(__DIR__).'/Resources/references.php';

        if (!\is_file($referenceFile)) {
            throw new \RuntimeException(\sprintf('File %s for ImageMagick references does not exist.'."\n".'Check that the file exists and that it is readable.', $referenceFile));
        }

        $config = require $referenceFile;

        $keysToCheck = [
            'colors',
            'colorspace_values',
            'interlace_types',
            'paper_sizes',
        ];
        $keysExist = true;

        foreach ($keysToCheck as $key) {
            if (!\array_key_exists($key, $config)) {
                $keysExist = false;
                break;
            }
        }

        if (\is_array($config) && $keysExist) {
            $this->config = $config;
        } else {
            throw new \InvalidArgumentException(\sprintf('ImageMagick references file "%s" seems to be empty or invalid.', $referenceFile));
        }
    }

    /**
     * @return string[]
     */
    public function getColorspaceValuesReference(): array
    {
        return $this->config['colorspace_values'];
    }

    /**
     * @return string[]
     */
    public function getColorsReference(): array
    {
        return $this->config['colors'];
    }

    /**
     * @return string[]
     */
    public function getInterlaceTypesReference(): array
    {
        return $this->config['interlace_types'];
    }

    public function getPaperSizes()
    {
        return $this->config['paper_sizes'];
    }

    /**
     * @param string|Geometry $geometry
     */
    public function geometry($geometry): string
    {
        if (!$geometry instanceof Geometry) {
            $geometry = new Geometry(\trim($geometry));
        }

        return $geometry->validate();
    }

    /**
     * @param string|Gravity $gravity
     */
    public function gravity($gravity): string
    {
        if (!$gravity instanceof Gravity) {
            $gravity = new Gravity(\trim($gravity));
        }

        return $gravity->validate();
    }

    /**
     * Checks that a color is correct according to ImageMagick command line reference.
     *
     * @see http://www.imagemagick.org/script/color.php
     */
    public function color(string $color): string
    {
        $color = \trim($color);
        if (
            // Check "hex"
            \preg_match('~^#(?:[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{12})$~i', $color)

            // Check "hexa"
            || \preg_match('~^#([a-f0-9]{8}|[a-f0-9]{16})$~i', $color)
            || \preg_match('~^rgb\(\d{1,3}(\.\d{1,2})?%?, ?\d{1,3}(\.\d{1,2})?%?, ?\d{1,3}(\.\d{1,2})?%?\)$~', $color)

            // Check "rgb"
            || \preg_match('~^rgba\(\d{1,3}(\.\d{1,2})?%?, ?\d{1,3}(\.\d{1,2})?%?, ?\d{1,3}(\.\d{1,2})?%?, ?[01](\.\d{1,6})?\)$~', $color)

            // Check "rgba"
            || \in_array($color, $this->getColorsReference(), true)// And check the dirty one : all the color names supported by ImageMagick
        ) {
            return $color;
        }

        throw new \InvalidArgumentException(\sprintf('The specified color (%s) is invalid.'."\n".'Please refer to ImageMagick command line documentation about colors:'."\n%s", $color, 'http://www.imagemagick.org/script/color.php'));
    }

    /**
     * Checks that colorspace value is valid in the references.
     */
    public function colorspace(string $colorspace): string
    {
        $colorspace = \trim($colorspace);

        $references = $this->getColorspaceValuesReference();

        if (\in_array($colorspace, $references, true)) {
            return $colorspace;
        }

        throw new \InvalidArgumentException(\sprintf('The specified colorspace value (%s) is invalid.'."\n".'The available values are:'."\n%s\n".'Please refer to ImageMagick command line documentation:'."\n%s", $colorspace, \implode(', ', $references), 'http://www.imagemagick.org/script/command-line-options.php#colorspace'));
    }

    /**
     * Checks that a rotation option is correct according to ImageMagick command line reference.
     *
     * @see http://www.imagemagick.org/script/command-line-options.php#rotate
     */
    public function rotation(string $rotation): string
    {
        $rotation = \trim($rotation);

        if (\preg_match('~^-?\d+(\.\d+)?[<>]?$~u', $rotation)) {
            return $rotation;
        }

        throw new \InvalidArgumentException(\sprintf('The specified rotate parameter (%s) is invalid.'."\n".'Please refer to ImageMagick command line documentation about the "-rotate" option:'."\n%s", $rotation, 'http://www.imagemagick.org/script/command-line-options.php#rotate'));
    }

    public function blur($blur): string
    {
        if (\is_string($blur)) {
            $blur = \trim($blur);
        }

        if (\is_numeric($blur)) {
            return (string) (float) $blur;
        }

        if (\preg_match('~^\d+(?:\.\d+)?(?:x\d+(?:\.\d+)?)?$~', $blur)) {
            return (string) $blur;
        }

        throw new \InvalidArgumentException(\sprintf('Gaussian blur must respect formats "%s" or "%s".'."\n".'Please refer to ImageMagick command line documentation about the "-gaussian-blur" and "-blur" options:'."\n%s\n%s", '{radius}', '{radius}x{sigma}', 'http://www.imagemagick.org/script/command-line-options.php#blur', 'http://www.imagemagick.org/script/command-line-options.php#gaussian-blur'));
    }

    /**
     * Checks that interlace type is valid in the references.
     */
    public function interlace(string $interlaceType): string
    {
        $interlaceType = \strtolower(\trim($interlaceType));

        $references = $this->getInterlaceTypesReference();

        if (\in_array($interlaceType, $references, true)) {
            return $interlaceType;
        }

        throw new \InvalidArgumentException(\sprintf('The specified interlace type (%s) is invalid.'."\n".'The available values are:'."\n%s\n".'Please refer to ImageMagick command line documentation:'."\n%s", $interlaceType, \implode(', ', $references), 'http://www.imagemagick.org/script/command-line-options.php#interlace'));
    }

    /**
     * Checks that threshold value is valid according to ImageMagick command line reference.
     */
    public function threshold(string $threshold): string
    {
        $threshold = \trim($threshold);

        if (\is_numeric($threshold)) {
            return $threshold;
        }

        if (\str_ends_with($threshold, '%')) {
            $percentNumber = \substr($threshold, 0, -1);
            if (\is_numeric($percentNumber)) {
                return $threshold;
            }
        }

        throw new \InvalidArgumentException(\sprintf('The specified threshold parameter (%s) is invalid.'."\n".'The value must be an integer or a percentage value'."\n".'Please refer to ImageMagick command line documentation:'."\n%s", $threshold, 'http://www.imagemagick.org/script/command-line-options.php#threshold'));
    }

    /**
     * @param string|Geometry $page
     */
    public function page($page): string
    {
        if ($page instanceof Geometry) {
            return $page->validate();
        }

        $paperSizesRegex = '(?<papersize>'.\implode('|', $this->getPaperSizes()).')';
        $offsetRegex = '[-+]\d+';
        $offsetsRegex = \sprintf('(?<offsetx>%s)?(?<offsety>%s)?', $offsetRegex, $offsetRegex);
        $ratioRegex = '(?<ratio>[^!<>])?';

        $pageOptionRegex = '~'.$paperSizesRegex.$offsetsRegex.$ratioRegex.'~i';

        if (!\preg_match($pageOptionRegex, $page)) {
            throw new \InvalidArgumentException(\sprintf('Page option is invalid.'."\n".'It must be either a Geometry value, or match the "%s" expression.'."\n".'Please refer to ImageMagick command line documentation:'."\n%s", 'media[offset][{^!<>}]', 'https://imagemagick.org/script/command-line-options.php#page'));
        }

        return $page;
    }
}
