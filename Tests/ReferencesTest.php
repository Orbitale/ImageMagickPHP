<?php
/*
* This file is part of the OrbitaleImageMagickPHP package.
*
* (c) Alexandre Rock Ancelet <alex@orbitale.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Orbitale\Component\ImageMagick\Tests;

use Orbitale\Component\ImageMagick\References;

class ReferencesTest extends \PHPUnit_Framework_TestCase
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
     * @dataProvider provideInvalidResourceFiles
     * @param $file
     * @param $expectedException
     */
    public function testInvalidResourceFile($file, $expectedException)
    {
        $exceptionClass = '';
        try {
            new References($file);
        } catch (\Exception $e) {
            $exceptionClass = get_class($e);
        }
        $this->assertEquals($exceptionClass, $expectedException);
    }

    public function provideInvalidResourceFiles()
    {
        return array(
            array('/this/directory/certainly/does/not/exist', 'RuntimeException'),
            array(TEST_RESOURCES_DIR.'/empty_reference.yml', 'InvalidArgumentException'),
            array(TEST_RESOURCES_DIR.'/tab_file.yml', 'Symfony\Component\Yaml\Exception\ParseException'),
        );
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
        $this->assertFalse($exception, sprintf("Failed in checking valid color (%s)", $color));
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
            sprintf("The specified color (%s) is invalid", $color),
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

    /**
     * @dataProvider provideCorrectGeometries
     * @param string $geometry
     */
    public function testCorrectGeometries($geometry)
    {
        $exception = false;
        try {
            $checked = $this->ref->geometry($geometry);
            $this->assertEquals($checked, trim($geometry));
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertFalse($exception, sprintf("Failed in checking valid geometry (%s)", $geometry));
    }

    public function provideCorrectGeometries()
    {
        return array(
            array('x100'),
            array('100x100'),
            array('100'),
            array('+0+0'),
            array('x100+0+0'),
            array('100+0+0'),
            array('100x100+0+0'),
            array('100%x100%+0+0'),
        );
    }

    /**
     * @dataProvider provideIncorrectGeometries
     * @param string $geometry
     */
    public function testIncorrectGeometries($geometry)
    {
        $msg = '';
        try {
            $this->ref->geometry($geometry);
        } catch (\InvalidArgumentException $e) {
            $msg = $e->getMessage();
        }
        $this->assertContains(
            sprintf("The specified geometry (%s) is invalid", $geometry),
            $msg
        );
    }

    public function provideIncorrectGeometries()
    {
        return array(
            array('100x'),
            array('+0'),
            array('x100+0'),
            array('100%x100%+0'),
        );
    }

}
