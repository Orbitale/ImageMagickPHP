<?php
/*
* This file is part of the PierstovalImageMagickPHP package.
*
* (c) Alexandre "Pierstoval" Rock Ancelet <pierstoval@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Pierstoval\Component\ImageMagick\Tests;

use Pierstoval\Component\ImageMagick\References;

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

    public function testColors()
    {

        $correctColors = array(
            '#000',
            ' #000 ',
            ' #000000 ',
            ' #000000000000 ',
            ' #0000000000000000 ',
            'black',
            'RosyBrown1',
            'rgb(0,0,0)',
            'rgb(0%,0%,0%)',
            'rgb(0, 0, 0)',
            'rgb(0.0,0.0,0.0)',
            'rgb(0.0%,0.0%,0.0%)',
            'rgba(0,0,0,0)',
            'rgba(0%,0%,0%,0.0)',
            'rgba(0, 0, 0, 0)',
            'rgba(0.0,0.0,0.0,1)',
            'rgba(0.0%,0.0%,0.0%,0.5)',
        );

        foreach ($correctColors as $correctColor) {
            try {
                $checked = $this->ref->color($correctColor);
                $this->assertEquals($checked, trim($correctColor));
            } catch (\InvalidArgumentException $e) {
                $this->assertTrue(false, sprintf("Failed in checking valid color (%s)", $correctColor));
            }
        }

        $wrongColors = array(
            'invalidColorName',
            '#0000',
            'rgb(0,0,0,0)',
            'rgba(0,0,0)',
            'rgba(0,0,0,2)',
            'rgba(0,0,0,1%)',
        );

        foreach ($wrongColors as $wrongColor) {
            try {
                $this->ref->color($wrongColor);
            } catch (\InvalidArgumentException $e) {
                $this->assertContains(
                    sprintf("The specified color (%s) is invalid", $wrongColor),
                    $e->getMessage()
                );
            }
        }

    }

    public function testGeometry()
    {

        $correctGeometries = array(
            'x100',
            '100x100',
            '100',
            '+0+0',
            'x100+0+0',
            '100+0+0',
            '100x100+0+0',
            '100%x100%+0+0',
        );

        foreach ($correctGeometries as $correctGeometry) {
            try {
                $checked = $this->ref->geometry($correctGeometry);
                $this->assertEquals($checked, trim($correctGeometry));
            } catch (\InvalidArgumentException $e) {
                $this->assertTrue(false, sprintf("Failed in checking valid geometry (%s)", $correctGeometry));
            }
        }

        $wrongGeometries = array(
            '100x',
            '+0',
            'x100+0',
            '100%x100%+0',
        );

        foreach ($wrongGeometries as $wrongGeometry) {
            try {
                $this->ref->geometry($wrongGeometry);
            } catch (\InvalidArgumentException $e) {
                $this->assertContains(
                    sprintf("The specified geometry (%s) is invalid", $wrongGeometry),
                    $e->getMessage()
                );
            }
        }
    }

}
