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

class ThresholdTest extends TestCase
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
     * @dataProvider provideValidThresholdValues
     */
    public function testValidThreshold(string $value): void
    {
        $threshold = $this->ref->threshold($value);

        self::assertSame($threshold, $value);
    }

    public function provideValidThresholdValues(): \Generator
    {
        yield '100' => ['100'];
        yield '-100' => ['-100'];
        yield '0' => ['0'];
        yield '100%' => ['100%'];
        yield '-100%' => ['-100%'];
        yield '0%' => ['0%'];
    }

    /**
     * @dataProvider provideInvalidThresholdValues
     */
    public function testInvalidThreshold(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'The specified threshold parameter (%s) is invalid.'."\n".'The value must be an integer or a percentage value'."\n".'Please refer to ImageMagick command line documentation:'."\n%s",
            $value,
            'http://www.imagemagick.org/script/command-line-options.php#threshold'
        ));

        $this->ref->threshold($value);
    }

    public function provideInvalidThresholdValues(): \Generator
    {
        yield 'Empty string' => [''];
        yield 'a' => ['a'];
        yield '-' => ['-'];
        yield '.' => ['.'];
        yield '1_00' => ['1_00'];
        yield '%' => ['%'];
        yield 'a%' => ['a%'];
        yield '-%' => ['-%'];
        yield '.%' => ['.%'];
        yield '1_00%' => ['1_00%'];
    }
}