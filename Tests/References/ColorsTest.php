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

class ColorsTest extends TestCase
{
    /**
     * @var References
     */
    private $ref;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->ref = new References();
    }

    /**
     * @dataProvider provideCorrectColors
     *
     * @param string $color
     */
    public function testCorrectColors($color): void
    {
        $exception = false;
        try {
            $checked = $this->ref->color($color);
            static::assertEquals($checked, \trim($color));
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        static::assertFalse($exception, \sprintf('Failed in checking valid color (%s)', $color));
    }

    public function provideCorrectColors(): ?\Generator
    {
        yield ['#000'];
        yield [' #000 '];
        yield [' #000000 '];
        yield [' #000000000000 '];
        yield [' #0000000000000000 '];
        yield ['black'];
        yield ['RosyBrown1'];
        yield ['LavenderBlush2'];
        yield ['rgb(0,0,0)'];
        yield ['rgb(0%,0%,0%)'];
        yield ['rgb(0, 0, 0)'];
        yield ['rgb(0.0,0.0,0.0)'];
        yield ['rgb(0.0%,0.0%,0.0%)'];
        yield ['rgba(0,0,0,0)'];
        yield ['rgba(0%,0%,0%,0.0)'];
        yield ['rgba(0, 0, 0, 0)'];
        yield ['rgba(0.0,0.0,0.0,1)'];
        yield ['rgba(0.0%,0.0%,0.0%,0.5)'];
    }

    /**
     * @dataProvider provideIncorrectColors
     *
     * @param string $color
     */
    public function testIncorrectColors($color): void
    {
        $msg = '';
        try {
            $this->ref->color($color);
        } catch (\InvalidArgumentException $e) {
            $msg = $e->getMessage();
        }
        static::assertStringContainsString(
            \sprintf('The specified color (%s) is invalid', $color),
            $msg
        );
    }

    public function provideIncorrectColors(): ?\Generator
    {
        yield ['invalidColorName'];
        yield ['#0000'];
        yield ['rgb(0,0,0,0)'];
        yield ['rgb(0)'];
        yield ['rgb()'];
        yield ['rgba(0,0,0)'];
        yield ['rgba(0)'];
        yield ['rgba()'];
        yield ['rgba(0,0,0,2)'];
        yield ['rgba(0,0,0,1%)'];
    }
}
