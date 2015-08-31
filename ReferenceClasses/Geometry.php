<?php
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
 * Represents an ImageMagick geometry parameter.
 * Each value is optional so a big regex is used to parse it.
 *
 * @link http://www.imagemagick.org/script/command-line-processing.php#geometry
 */
class Geometry
{
    const REGEX_VALIDATE = '~^(?:x?[0-9]+(?:\.[0-9]+)?%?|[0-9]+(?:\.[0-9]+)?%?x[0-9]+(?:\.[0-9]+)?%?[\^!<>]?)?(?:[+-][0-9]+[+-][0-9]+)?$~';

    const RATIO_NONE    = null;
    const RATIO_MIN     = '^';
    const RATIO_IGNORE  = '!';
    const RATIO_SHRINK  = '>';
    const RATIO_ENLARGE = '<';

    private static $validRatios = array(self::RATIO_ENLARGE, self::RATIO_IGNORE, self::RATIO_MIN, self::RATIO_SHRINK);

    /**
     * @var string
     */
    private $value = '';

    public function __construct($width = null, $height = null, $x = null, $y = null, $aspectRatio = self::RATIO_NONE)
    {
        $args = func_get_args();
        if (count(array_map(null, $args)) > 1) {
            $geometry = call_user_func_array(array($this, 'createFromParameters'), $args);
        } else {
            $geometry = $width;
        }
        $this->value = $geometry;
    }

    /**
     * @param string|int $width Can be both
     * @param string|int $height
     * @param string|int $x
     * @param string|int $y
     * @param string     $aspectRatio
     *
     * @return static
     */
    public static function createFromParameters($width = null, $height = null, $x = null, $y = null, $aspectRatio = self::RATIO_NONE)
    {
        $geometry = $width;
        if (null !== $height) {
            $geometry .= 'x'.$height;
        }

        if ($aspectRatio && !in_array($aspectRatio, self::$validRatios)) {
            throw new \InvalidArgumentException(sprintf(
                "Invalid aspect ratio value to generate geometry, \"%s\" given.\nAvailable: %s",
                $aspectRatio, implode(', ', self::$validRatios)
            ));
        }
        $geometry .= $aspectRatio;

        if (null !== $x) {
            $geometry .= ($x >= 0 ? '+' : '-').abs($x);
            if (null !== $y) {
                $geometry .= ($y >= 0 ? '+' : '-').abs($y);
            }
        } elseif (null !== $y) {
            if (null !== $y) {
                $geometry .= '+0'.($y >= 0 ? '+' : '-').abs($y);
            }
        }

        return $geometry;
    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     */
    public function validate()
    {
        if (
        !preg_match(static::REGEX_VALIDATE, $this->value)
        ) {
            throw new \InvalidArgumentException(sprintf(
                "The specified geometry (%s) is invalid.\n".
                "Please refer to ImageMagick command line documentation about geometry:\n%s",
                $this->value,
                'http://www.imagemagick.org/script/command-line-processing.php#geometry'
            ));
        }

        return $this->value;
    }
}
