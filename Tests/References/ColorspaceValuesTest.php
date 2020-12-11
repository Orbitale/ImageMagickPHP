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

class ColorspaceValuesTest extends TestCase
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
     * @param string $colorspaceValue
     * @param string $expected
     *
     * @dataProvider provideValidColorspaceValues
     */
    public function testValidColorspaceValue($colorspaceValue, $expected): void
    {
        $validatedType = $this->ref->colorspace($colorspaceValue);

        static::assertSame($expected, $validatedType);
    }

    public function provideValidColorspaceValues(): ?\Generator
    {
        yield ['CMY', 'CMY'];
        yield ['CMYK', 'CMYK'];
        yield ['Gray', 'Gray'];
        yield ['HCL', 'HCL'];
        yield ['HCLp', 'HCLp'];
        yield ['HSB', 'HSB'];
        yield ['HSI', 'HSI'];
    }

    /**
     * @dataProvider provideInvalidColorspaceValues
     */
    public function testInvalidColorspaceValues($colorspaceValue): void
    {
        $colorspaceValue = (string) $colorspaceValue;

        $msg = '';
        try {
            $this->ref->colorspace($colorspaceValue);
        } catch (\InvalidArgumentException $e) {
            $msg = $e->getMessage();
        }
        static::assertStringContainsString(
            \sprintf('The specified colorspace value (%s) is invalid', \trim($colorspaceValue)),
            $msg
        );
    }

    public function provideInvalidColorspaceValues(): ?\Generator
    {
        yield [1];
        yield ['2'];
        yield ['wow'];
        yield ['WoW'];
        yield [''];
        yield [' '];
    }
}
