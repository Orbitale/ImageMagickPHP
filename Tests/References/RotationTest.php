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

class RotationTest extends TestCase
{
    /**
     * @dataProvider provideValidRotationValues
     */
    public function testValidRotation(string $value): void
    {
        static::assertSame(\trim($value), (new References())->rotation($value));
    }

    public function provideValidRotationValues(): \Generator
    {
        yield ['1'];
        yield ['-1'];
        yield ['   360'];
        yield ['-360    '];
        yield ['1>'];
        yield ['-1>'];
        yield ['360.5>'];
        yield ['-360.5>'];
        yield ['1<'];
        yield ['-1<'];
        yield ['   360<'];
        yield ['-360<   '];
    }

    /**
     * @dataProvider provideInvalidRotationValues
     */
    public function testInvalidRotation(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('The specified rotate parameter (%s) is invalid.'."\n".'Please refer to ImageMagick command line documentation about the "-rotate" option:'."\n%s", \trim($value), 'http://www.imagemagick.org/script/command-line-options.php#rotate'));

        static::assertSame($value, (new References())->rotation($value));
    }

    public function provideInvalidRotationValues(): \Generator
    {
        yield ['a'];
        yield ['-1a'];
        yield ['   abc   '];
        yield ['0,5'];
        yield [''];
    }
}
