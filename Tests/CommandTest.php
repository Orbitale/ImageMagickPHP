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

use Pierstoval\Component\ImageMagick\Command;

class CommandTest extends \PHPUnit_Framework_TestCase
{

    private $resourcesDir;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        if (!defined('IMAGEMAGICK_DIR') || !defined('TEST_RESOURCES_DIR')) {
            throw new \RuntimeException(
                "The \"IMAGEMAGICK_DIR\" constant is not defined.\n" .
                "The bootstrap must be correctly included before executing test suite."
            );
        }
        $this->resourcesDir = TEST_RESOURCES_DIR;
    }

    public function setUp()
    {
        $dir = TEST_RESOURCES_DIR.'/outputs';
        foreach (scandir($dir) as $file) {
            if ('.' !== $file && '..' !== $file && '.gitkeep' !== $file) {
                unlink($dir.'/'.$file);
            }
        }
    }

    public function testResizeImage()
    {

        $command = new Command(IMAGEMAGICK_DIR);

        $imageToResize = $this->resourcesDir . '/moon_180.jpg';
        $imageOutput = $this->resourcesDir . '/outputs/moon.jpg';
        $this->assertFileExists($imageToResize);

        $command
            ->convert($imageToResize)
            ->resize('100x100')
            ->file($imageOutput, false);

        $response = $command->run();

        $this->assertFileExists($this->resourcesDir . '/outputs/moon.jpg');

        $this->assertFalse($response->hasFailed());

        $this->testIdentifyImage($imageOutput, 'JPEG', '100x94+0+0', '8-bit', 'sRGB');

    }

    /**
     * @dataProvider provideImagesToIdentify
     */
    public function testIdentifyImage($imageToIdentify, $expectedFormat, $expectedGeometry, $expectedResolution, $expectedColorFormat)
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $response = $command->identify($imageToIdentify)->run();

        $this->assertFalse($response->hasFailed());

        $content = $response->getContent();
        $content = implode("\n", $content);

        $this->assertContains(sprintf(
            '%s %s %s %s %s %s',
            $imageToIdentify,
            $expectedFormat,
            preg_replace('~\+.*$~', '', $expectedGeometry),
            $expectedGeometry,
            $expectedResolution,
            $expectedColorFormat
        ), $content);
    }

    public function provideImagesToIdentify()
    {
        return array(
            array($this->resourcesDir.'/moon_180.jpg', 'JPEG', '180x170+0+0', '8-bit', 'sRGB'),
        );
    }
}
