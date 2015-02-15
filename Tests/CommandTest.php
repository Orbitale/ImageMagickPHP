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

    /**
     * @dataProvider provideWrongConvertDirs
     */
    public function testWrongConvertDirs($path, $expectedMessage, $expectedException)
    {
        $exception = '';
        $exceptionClass = '';

        $ds = DIRECTORY_SEPARATOR;
        $path = str_replace(array('/', '\\'), array($ds, $ds), $path);
        $expectedMessage = str_replace(array('/', '\\'), array($ds, $ds), $expectedMessage);
        try {
            new Command($path);
        } catch (\Exception $e) {
            $exception = $e->getMessage();
            $exceptionClass = get_class($e);
        }
        $this->assertContains($expectedMessage, $exception);
        $this->assertEquals($exceptionClass, $expectedException);
    }

    public function provideWrongConvertDirs()
    {
        return array(
            array('/this/is/a/dummy/dir', 'The specified path (/this/is/a/dummy/dir/) is not a directory', 'InvalidArgumentException'),
            array('./', 'The specified path (./) does not seem to contain ImageMagick binaries, or it is not readable', 'InvalidArgumentException'),
            array(TEST_RESOURCES_DIR.'/', 'ImageMagick does not seem to work well, the test command resulted in an error', 'InvalidArgumentException'),
        );
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

        $this->testIdentifyImage($imageOutput, 'JPEG', '100x94+0+0', '8-bit');
    }

    /**
     * @dataProvider provideImagesToIdentify
     */
    public function testIdentifyImage($imageToIdentify, $expectedFormat, $expectedGeometry, $expectedResolution)
    {
        $command = new Command(IMAGEMAGICK_DIR);

        // ImageMagick normalizes paths with "/" as directory separator
        $imageToIdentify = str_replace('\\', '/', $imageToIdentify);

        $response = $command->identify($imageToIdentify)->run();

        $this->assertFalse($response->hasFailed());

        $content = $response->getContent();
        $content = implode("\n", $content);

        $this->assertContains(sprintf(
            '%s %s %s %s %s',
            $imageToIdentify,
            $expectedFormat,
            preg_replace('~\+.*$~', '', $expectedGeometry),
            $expectedGeometry,
            $expectedResolution
        ), $content);
    }

    public function provideImagesToIdentify()
    {
        return array(
            array($this->resourcesDir.'/moon_180.jpg', 'JPEG', '180x170+0+0', '8-bit'),
        );
    }
}
