<?php

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

    public function __construct($name = null, array $data = array(), $dataName = '')
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
    public function testValidInterlaceTypes($interlaceType, $expected)
    {
        $validatedType = $this->ref->interlace($interlaceType);

        static::assertSame($expected, $validatedType);
    }

    public function provideValidInterlaceTypes()
    {
        return [
            ['none', 'none'],
            ['line', 'line'],
            ['plane', 'plane'],
            ['partition', 'partition'],
            ['jpeg', 'jpeg'],
            ['gif', 'gif'],
            ['png', 'png'],
            ['PNG', 'png'],
            [' none ', 'none'],
            ['NONE', 'none'],
            ['NONE', 'none'],
        ];
    }

    /**
     * @param string $interlaceType
     *
     * @dataProvider provideInvalidInterlaceTypes
     */
    public function testInvalidInterlaceTypes($interlaceType)
    {
        $msg = '';
        try {
            $this->ref->interlace($interlaceType);
        } catch (\InvalidArgumentException $e) {
            $msg = $e->getMessage();
        }
        $this->assertContains(
            sprintf('The specified interlace type (%s) is invalid', strtolower(trim($interlaceType))),
            $msg
        );
    }

    public function provideInvalidInterlaceTypes()
    {
        return [
            [1],
            ['2'],
            ['wow'],
            [''],
            [' '],
            [0x00],
        ];
    }
}
