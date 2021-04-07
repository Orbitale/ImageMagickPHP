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

use Orbitale\Component\ImageMagick\ReferenceClasses\Geometry;
use Orbitale\Component\ImageMagick\References;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    /**
     * @param string|Geometry $pageValue
     *
     * @dataProvider provideValidPageValues
     */
    public function testValidPageValue($pageValue, string $expectedValue): void
    {
        self::assertSame($expectedValue, (new References())->page($pageValue));
    }

    public function provideValidPageValues(): \Generator
    {
        $done = [];

        yield 'Geometry' => [new Geometry(1, 2, 3, 4, '^'), '1x2^+3+4'];

        foreach ((new References())->getPaperSizes() as $paperSize) {
            foreach (['', '+1', '-1'] as $offsetX) {
                foreach (['', '+1', '-1'] as $offsetY) {
                    foreach (['', '^', '!', '<', '>', '{', '}'] as $aspectRatio) {
                        $pageString = $paperSize.$offsetX.$offsetY.$aspectRatio;
                        if (isset($done[$pageString])) {
                            continue;
                        }

                        $done[$pageString] = true;

                        yield $pageString => [$pageString, $pageString];
                    }
                }
            }
        }
    }

    /**
     * @dataProvider provideInvalidPageValues
     */
    public function testInvalidPageValue(string $pageString): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Page option is invalid.\nIt must be either a Geometry value, or match the \"media[offset][{^!<>}]\" expression.\nPlease refer to ImageMagick command line documentation:\nhttps://imagemagick.org/script/command-line-options.php#page");

        (new References())->page($pageString);
    }

    public function provideInvalidPageValues(): \Generator
    {
        yield '+1' => ['+1'];
    }
}
