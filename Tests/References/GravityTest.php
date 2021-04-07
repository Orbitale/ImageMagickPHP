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

namespace Orbitale\Component\ImageMagick\Tests\References;

use Orbitale\Component\ImageMagick\Command;
use Orbitale\Component\ImageMagick\ReferenceClasses\Gravity;
use Orbitale\Component\ImageMagick\Tests\AbstractTestCase;

class GravityTest extends AbstractTestCase
{
    /**
     * @param string $gravity
     *
     * @dataProvider provideValidGravities
     */
    public function testGravity($gravity): void
    {
        $gravity = new Gravity($gravity);

        $validatedGravity = $gravity->validate();

        static::assertNotEmpty($validatedGravity);

        $command = new Command(IMAGEMAGICK_DIR);

        $outputFile = $this->resourcesDir.'/outputs/moon_180_test_gravity_'.\md5($gravity.'test_geo').'.jpg';

        if (\file_exists($outputFile)) {
            \unlink($outputFile);
        }

        $command
            ->convert($this->resourcesDir.'/moon_180.jpg')
            ->gravity($gravity)
            ->resize('100x100')
            ->file($outputFile, false)
        ;

        $response = $command->run();

        static::assertTrue($response->isSuccessful(), \sprintf(
            "Gravity fixture \"%s\" returned an ImageMagick error.\nFull command: %s\nErrors:\n%s\n%s",
            $gravity->validate(),
            $command->getCommand(),
            $response->getOutput(),
            $response->getError()
        ));
        static::assertFileExists($outputFile);
    }

    public function provideValidGravities(): ?\Generator
    {
        yield 0 => ['NorthWest'];
        yield 1 => ['North'];
        yield 2 => ['NorthEast'];
        yield 3 => ['West'];
        yield 4 => ['Center'];
        yield 5 => ['East'];
        yield 6 => ['SouthWest'];
        yield 7 => ['South'];
        yield 8 => ['SouthEast'];
    }

    /**
     * @param string $gravity
     *
     * @dataProvider provideWrongGravities
     */
    public function testWrongGravities($gravity): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid gravity option, "'.$gravity."\" given.\nAvailable: NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast");

        $testGravity = new Gravity($gravity);
        $testGravity->validate();
    }

    public function provideWrongGravities(): ?\Generator
    {
        yield 0 => ['Northwest'];
        yield 1 => ['northwest'];
        yield 2 => ['north'];
        yield 3 => ['northEast'];
        yield 4 => ['Northeast'];
        yield 5 => ['west'];
        yield 6 => ['center'];
        yield 7 => ['east'];
        yield 8 => ['southwest'];
        yield 9 => ['south'];
        yield 10 => ['southeast'];
        yield 11 => ['Middle'];
        yield 12 => [''];
        yield 13 => [' '];
    }
}
