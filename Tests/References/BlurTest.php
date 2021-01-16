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

use Orbitale\Component\ImageMagick\References;
use PHPUnit\Framework\TestCase;

class BlurTest extends TestCase
{
    /**
     * @dataProvider provideInvalidOptions
     */
    public function testInvalidBlurOptions($invalidOption): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Gaussian blur must respect formats "{radius}" or "{radius}x{sigma}".'."\n"
            .'Please refer to ImageMagick command line documentation about the "-gaussian-blur" and "-blur" options:'."\n"
            .'http://www.imagemagick.org/script/command-line-options.php#blur'."\n"
            .'http://www.imagemagick.org/script/command-line-options.php#gaussian-blur');
        $this->getReferences()->blur($invalidOption);
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testValidBlurOptions($validOption, $expectedReturn): void
    {
        static::assertSame($expectedReturn, $this->getReferences()->blur($validOption));
    }

    public function getReferences(): References
    {
        return new References();
    }

    public function provideInvalidOptions(): \Generator
    {
        yield ['invalid'];
        yield ['axb'];
        yield ['   a x   b'];
        yield ['1x2x3'];
        yield ['1  x2'];
        yield ['1x  2'];
    }

    public function provideValidOptions(): \Generator
    {
        yield [1, '1'];
        yield [-1, '-1'];
        yield ['1', '1'];
        yield ['-1', '-1'];
        yield ['1x2', '1x2'];
        yield [' 1x2 ', '1x2'];
    }
}
