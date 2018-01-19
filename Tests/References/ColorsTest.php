<?php

/*
 * This file is part of the OrbitaleImageMagickPHP package.
 *
 * (c) Alexandre Rock Ancelet <alex@orbitale.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orbitale\Component\ImageMagick\Tests\References;

use Orbitale\Component\ImageMagick\References;
use PHPUnit\Framework\TestCase;

class ColorsTest extends TestCase
{
    /**
     * @var References
     */
    private $ref;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->ref = new References();
    }

    /**
     * @dataProvider provideCorrectColors
     * @param string $color
     */
    public function testCorrectColors($color)
    {
        $exception = false;
        try {
            $checked = $this->ref->color($color);
            $this->assertEquals($checked, trim($color));
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertFalse($exception, sprintf('Failed in checking valid color (%s)', $color));
    }

    public function provideCorrectColors()
    {
        return array(
            array('#000'),
            array(' #000 '),
            array(' #000000 '),
            array(' #000000000000 '),
            array(' #0000000000000000 '),
            array('black'),
            array('RosyBrown1'),
            array('LavenderBlush2'),
            array('rgb(0,0,0)'),
            array('rgb(0%,0%,0%)'),
            array('rgb(0, 0, 0)'),
            array('rgb(0.0,0.0,0.0)'),
            array('rgb(0.0%,0.0%,0.0%)'),
            array('rgba(0,0,0,0)'),
            array('rgba(0%,0%,0%,0.0)'),
            array('rgba(0, 0, 0, 0)'),
            array('rgba(0.0,0.0,0.0,1)'),
            array('rgba(0.0%,0.0%,0.0%,0.5)'),
        );
    }

    /**
     * @dataProvider provideIncorrectColors
     * @param string $color
     */
    public function testIncorrectColors($color)
    {
        $msg = '';
        try {
            $this->ref->color($color);
        } catch (\InvalidArgumentException $e) {
            $msg = $e->getMessage();
        }
        $this->assertContains(
            sprintf('The specified color (%s) is invalid', $color),
            $msg
        );
    }

    public function provideIncorrectColors()
    {
        return array(
            array('invalidColorName'),
            array('#0000'),
            array('rgb(0,0,0,0)'),
            array('rgb(0)'),
            array('rgb()'),
            array('rgba(0,0,0)'),
            array('rgba(0)'),
            array('rgba()'),
            array('rgba(0,0,0,2)'),
            array('rgba(0,0,0,1%)'),
        );
    }

}
