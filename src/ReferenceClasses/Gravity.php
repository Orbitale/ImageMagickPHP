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

namespace Orbitale\Component\ImageMagick\ReferenceClasses;

/**
 * Represents an ImageMagick gravity parameter.
 *
 * @see https://www.imagemagick.org/script/command-line-options.php#gravity
 */
class Gravity
{
    private static $validGravity = [
        'NorthWest',
        'North',
        'NorthEast',
        'West',
        'Center',
        'East',
        'SouthWest',
        'South',
        'SouthEast',
    ];

    /**
     * @var string
     */
    private $value;

    public function __construct(string $gravity)
    {
        $this->value = $gravity;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function validate(): string
    {
        if (!\in_array($this->value, self::$validGravity, true)) {
            throw new \InvalidArgumentException(\sprintf("Invalid gravity option, \"%s\" given.\nAvailable: %s", $this->value, \implode(', ', self::$validGravity)));
        }

        return $this->value;
    }
}
