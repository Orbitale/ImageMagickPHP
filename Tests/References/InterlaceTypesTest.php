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

class InterlaceTypesTest extends TestCase
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
     * @param string $interlaceType
     * @param string $expected
     *
     * @dataProvider provideValidInterlaceTypes
     */
    public function testValidInterlaceTypes($interlaceType, $expected): void
    {
        $validatedType = $this->ref->interlace($interlaceType);

        static::assertSame($expected, $validatedType);
    }

    public function provideValidInterlaceTypes(): ?\Generator
    {
        yield ['none', 'none'];
        yield ['line', 'line'];
        yield ['plane', 'plane'];
        yield ['partition', 'partition'];
        yield ['jpeg', 'jpeg'];
        yield ['gif', 'gif'];
        yield ['png', 'png'];
        yield ['PNG', 'png'];
        yield [' none ', 'none'];
        yield ['NONE', 'none'];
        yield ['NONE', 'none'];
    }

    /**
     * @dataProvider provideInvalidInterlaceTypes
     */
    public function testInvalidInterlaceTypes($interlaceType): void
    {
        $interlaceType = (string) $interlaceType;

        $msg = '';
        try {
            $this->ref->interlace($interlaceType);
        } catch (\InvalidArgumentException $e) {
            $msg = $e->getMessage();
        }
        static::assertStringContainsString(
            \sprintf('The specified interlace type (%s) is invalid', \strtolower(\trim($interlaceType))),
            $msg
        );
    }

    public function provideInvalidInterlaceTypes(): ?\Generator
    {
        yield [1];
        yield ['2'];
        yield ['wow'];
        yield ['WoW'];
        yield [''];
        yield [' '];
        yield [0x00];
    }
}
