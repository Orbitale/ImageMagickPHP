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

namespace Orbitale\Component\ImageMagick\Tests;

use Orbitale\Component\ImageMagick\Command;
use Orbitale\Component\ImageMagick\MagickBinaryNotFoundException;

class CommandTest extends AbstractTestCase
{
    /**
     * @dataProvider provideWrongConvertDirs
     */
    public function testWrongConvertDirs($path, $expectedMessage, $expectedException): void
    {
        $path = \str_replace('\\', '/', $path);
        $expectedMessage = \str_replace('\\', '/', $expectedMessage);
        $this->expectExceptionMessage($expectedMessage);
        $this->expectException($expectedException);

        new Command($path);
    }

    public function provideWrongConvertDirs(): ?\Generator
    {
        yield ['/this/is/a/dummy/dir', "ImageMagick does not seem to work well, the test command resulted in an error.\nExecution returned message: \"Command not found\"\nTo solve this issue, please run this command and check your error messages to see if ImageMagick was correctly installed:\n/this/is/a/dummy/dir -version", MagickBinaryNotFoundException::class];
        yield ['./', "The specified path (\"ImageMagick does not seem to work well, the test command resulted in an error.\nExecution returned message: \"Misuse of shell builtins\"\nTo solve this issue, please run this command and check your error messages to see if ImageMagick was correctly installed:\n. -version\") is not a file.\nYou must set the \"magickBinaryPath\" parameter as the main \"magick\" binary installed by ImageMagick.", MagickBinaryNotFoundException::class];
    }

    /**
     * @param $fileSources
     *
     * @dataProvider convertDataProvider
     */
    public function testConvert($fileSources, string $fileOutput): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $response = $command
            ->convert($fileSources)
            ->output($fileOutput)
            ->run()
        ;

        static::assertFileExists($fileOutput);

        static::assertFalse($response->hasFailed());
    }

    public function convertDataProvider(): array
    {
        $singleSource = $this->resourcesDir.'/moon_180.jpg';
        $multipleSources = [
            $this->resourcesDir.'/moon_180.jpg',
            $this->resourcesDir.'/dabug.png',
        ];

        return [
            [$singleSource, $this->resourcesDir.'/outputs/output1.pdf'],
            [$multipleSources, $this->resourcesDir.'/outputs/output2.pdf'],
        ];
    }

    public function testResizeImage(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $imageToResize = $this->resourcesDir.'/moon_180.jpg';
        $imageOutput = $this->resourcesDir.'/outputs/moon.jpg';
        static::assertFileExists($imageToResize);

        $response = $command
            ->convert($imageToResize)
            ->resize('100x100')
            ->file($imageOutput, false)
            ->run()
        ;

        static::assertFalse($response->hasFailed(), "Errors when testing:\n".$response->getProcess()->getOutput()."\t".$response->getProcess()->getErrorOutput());

        static::assertFileExists($this->resourcesDir.'/outputs/moon.jpg');

        static::assertFalse($response->hasFailed());

        $this->testConvertIdentifyImage($imageOutput, 'JPEG', '100x94+0+0', '8-bit');
    }

    public function testDepthImage(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $imageToResize = $this->resourcesDir.'/moon_180.jpg';
        $imageOutput = $this->resourcesDir.'/outputs/moon.jpg';
        static::assertFileExists($imageToResize);

        $response = $command
            ->convert($imageToResize)
            ->depth(1)
            ->file($imageOutput, false)
            ->run()
        ;

        static::assertFalse($response->hasFailed(), "Errors when testing:\n".$response->getProcess()->getOutput()."\t".$response->getProcess()->getErrorOutput());

        static::assertFileExists($this->resourcesDir.'/outputs/moon.jpg');

        static::assertFalse($response->hasFailed());

        $this->testConvertIdentifyImage($imageOutput, 'JPEG', '180x170+0+0', '8-bit');
    }

    public function testFlattenImage(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $imageToResize = $this->resourcesDir.'/moon_180.jpg';
        $imageOutput = $this->resourcesDir.'/outputs/moon.jpg';
        static::assertFileExists($imageToResize);

        $response = $command
            ->convert($imageToResize)
            ->flatten()
            ->file($imageOutput, false)
            ->run()
        ;

        static::assertFalse($response->hasFailed(), "Errors when testing:\n".$response->getProcess()->getOutput()."\t".$response->getProcess()->getErrorOutput());

        static::assertFileExists($this->resourcesDir.'/outputs/moon.jpg');

        static::assertFalse($response->hasFailed());

        $this->testConvertIdentifyImage($imageOutput, 'JPEG', '180x170+0+0', '8-bit');
    }

    public function testTranspose(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $imageSource = $this->resourcesDir.'/moon_180.jpg';
        $imageOutput = $this->resourcesDir.'/outputs/moon_transpose.jpg';
        static::assertFileExists($imageSource);

        $response = $command
            ->convert($imageSource)
            ->transpose()
            ->file($imageOutput, false)
            ->run()
        ;

        static::assertFalse($response->hasFailed());
        static::assertFileExists($this->resourcesDir.'/outputs/moon_transpose.jpg');
    }

    public function testTransverse(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $imageSource = $this->resourcesDir.'/moon_180.jpg';
        $imageOutput = $this->resourcesDir.'/outputs/moon_transverse.jpg';
        static::assertFileExists($imageSource);

        $response = $command
            ->convert($imageSource)
            ->transverse()
            ->file($imageOutput, false)
            ->run()
        ;

        static::assertFalse($response->hasFailed());
        static::assertFileExists($this->resourcesDir.'/outputs/moon_transverse.jpg');
    }

    public function testColorspaceImage(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $imageToResize = $this->resourcesDir.'/moon_180.jpg';
        $imageOutput = $this->resourcesDir.'/outputs/moon.jpg';
        static::assertFileExists($imageToResize);

        $response = $command
            ->convert($imageToResize)
            ->colorspace('Gray')
            ->file($imageOutput, false)
            ->run()
        ;

        static::assertFalse($response->hasFailed(), "Errors when testing:\n".$response->getProcess()->getOutput()."\t".$response->getProcess()->getErrorOutput());

        static::assertFileExists($this->resourcesDir.'/outputs/moon.jpg');

        static::assertFalse($response->hasFailed());

        $this->testConvertIdentifyImage($imageOutput, 'JPEG', '180x170+0+0', '8-bit');
    }

    public function testMonochrome(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $imageSource = $this->resourcesDir.'/moon_180.jpg';
        $imageOutput = $this->resourcesDir.'/outputs/moon_monochrome.jpg';
        static::assertFileExists($imageSource);

        $response = $command
            ->convert($imageSource)
            ->monochrome()
            ->file($imageOutput, false)
            ->run()
        ;

        static::assertFalse($response->hasFailed());
        static::assertFileExists($this->resourcesDir.'/outputs/moon_monochrome.jpg');
    }

    public function testGravityCommand(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $imageToResize = $this->resourcesDir.'/moon_180.jpg';
        $imageOutput = $this->resourcesDir.'/outputs/moon.jpg';
        static::assertFileExists($imageToResize);

        $response = $command
            ->convert($imageToResize)
            ->gravity('Center')
            ->extent('100x100')
            ->file($imageOutput, false)
            ->run()
        ;

        static::assertFalse($response->hasFailed(), "Errors when testing:\n".$response->getProcess()->getOutput()."\t".$response->getProcess()->getErrorOutput());

        static::assertFileExists($this->resourcesDir.'/outputs/moon.jpg');

        $this->testConvertIdentifyImage($imageOutput, 'JPEG', '100x100+0+0', '8-bit');
    }

    /**
     * @dataProvider provideImagesToIdentify
     */
    public function testConvertIdentifyImage($imageToIdentify, $expectedFormat, $expectedGeometry, $expectedResolution): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        // ImageMagick normalizes paths with "/" as directory separator
        $imageToIdentify = \str_replace('\\', '/', $imageToIdentify);

        $response = $command->identify($imageToIdentify)->run();

        static::assertFalse($response->hasFailed());

        $content = $response->getOutput();

        static::assertStringContainsString(\sprintf(
            '%s %s %s %s %s',
            $imageToIdentify,
            $expectedFormat,
            \preg_replace('~\+.*$~', '', $expectedGeometry),
            $expectedGeometry,
            $expectedResolution
        ), $content);
    }

    public function provideImagesToIdentify(): ?\Generator
    {
        yield [$this->resourcesDir.'/moon_180.jpg', 'JPEG', '180x170+0+0', '8-bit'];
    }

    public function testMogrifyResizeImage(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $sourceImage = $this->resourcesDir.'/moon_180.jpg';
        $imageOutput = $this->resourcesDir.'/outputs/moon_mogrify.jpg';
        static::assertFileExists($sourceImage);

        $baseSize = \filesize($sourceImage);

        if (\file_exists($imageOutput)) {
            \unlink($imageOutput);
        }

        \copy($sourceImage, $imageOutput);

        \clearstatcache(true, $imageOutput);

        if (!\file_exists($imageOutput)) {
            static::fail('File could not be copied from resources dir to output dir.');
        }

        $response = $command
            ->mogrify($imageOutput)
            ->background('#000000')
            ->extent('5000x5000')
            ->run()
        ;

        static::assertFileExists($imageOutput);

        static::assertTrue($response->isSuccessful(), "Command returned an error when testing mogrify resize:\n".$response->getOutput()."\n".$response->getError());

        static::assertGreaterThan($baseSize, \filesize($imageOutput));
    }

    /**
     * @dataProvider provideTestCommandString
     */
    public function testCommandString($source, $output, $geometry, $quality, $format): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $commandString = $command
            ->convert($source)
            ->thumbnail($geometry)
            ->quality($quality)
            ->page($format)
            ->file($output, false)
            ->getCommand()
        ;

        $expected = \implode(' ', $command->getExecutable('convert')).
                    ' '.$source.
                    ' -thumbnail "'.$geometry.'"'.
                    ' -quality '.$quality.
                    ' -page "'.$format.'"'.
                    ' '.$output;

        $expected = \str_replace('\\', '/', $expected);

        static::assertEquals($expected, $commandString);
    }

    public function provideTestCommandString(): ?\Generator
    {
        yield 0 => [$this->resourcesDir.'/moon_180.jpg', $this->resourcesDir.'/outputs/moon_10_forced.jpg', '10x10!', 10, 'a4'];
        yield 1 => [$this->resourcesDir.'/moon_180.jpg', $this->resourcesDir.'/outputs/moon_1000.jpg', '1000x1000', 100, '9x11'];
        yield 2 => [$this->resourcesDir.'/moon_180.jpg', $this->resourcesDir.'/outputs/moon_half.jpg', '50%', 50, 'halfletter'];
        yield 3 => [$this->resourcesDir.'/moon_180.jpg', $this->resourcesDir.'/outputs/moon_geometry.jpg', '30x30+20+20', 50, 'Letter+43+43'];
    }

    public function testWrongExecutable(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $command = new Command(IMAGEMAGICK_DIR);
        $command->getExecutable('this_executable_might_not_exist');
    }

    public function testInexistingFiles(): void
    {
        $command = new Command(IMAGEMAGICK_DIR);

        $exception = '';
        $file = __DIR__.'/this/file/does/not/exist';
        try {
            $command->file($file, true, true);
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }
        static::assertStringContainsString(\sprintf('The file "%s" is not found.', $file), $exception);
    }
}
