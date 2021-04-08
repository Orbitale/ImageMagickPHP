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
use Orbitale\Component\ImageMagick\ReferenceClasses\Geometry;
use Orbitale\Component\ImageMagick\Tests\AbstractTestCase;

class GeometryTest extends AbstractTestCase
{
    /**
     * @dataProvider provideValidGeometries
     */
    public function testGeometry(?int $width, ?int $height, ?int $x, ?int $y, ?string $aspectRatio): void
    {
        $geometry = new Geometry($width, $height, $x, $y, $aspectRatio);

        try {
            $validatedGeometry = $geometry->validate();
        } catch (\Exception $e) {
            static::fail($e->getMessage());

            return;
        }

        static::assertIsString($validatedGeometry);
        static::assertNotEmpty($validatedGeometry);

        if ('' === $validatedGeometry) {
            static::markTestSkipped('No geometry to check. ['.$validatedGeometry.'] ['.\implode(',', \func_get_args()).']');
        }

        $command = new Command(IMAGEMAGICK_DIR);

        $outputFile = $this->resourcesDir.'/outputs/moon_180_test_geometry_'.\md5($width.$height.$x.$y.$aspectRatio.'test_geo').'.jpg';

        if (\file_exists($outputFile)) {
            \unlink($outputFile);
        }

        $command
            ->convert($this->resourcesDir.'/moon_180.jpg')
            ->resize($geometry)
            ->file($outputFile, false)
        ;

        $response = $command->run();

        static::assertTrue($response->isSuccessful(), \sprintf(
            "Geometry fixture \"%s\" returned an ImageMagick error.\nFull command: %s\nErrors:\n%s\n%s",
            $geometry->validate(),
            $command->getCommand(),
            $response->getOutput(),
            $response->getError()
        ));
        static::assertFileExists($outputFile);
    }

    public function provideValidGeometries(): ?\Generator
    {
        // width, height, x, y, aspectRatio
        yield 0 => [null, null, null, 0, null];
        yield 1 => [null, null, null, -1, null];
        yield 2 => [null, null, null, 1, null];
        yield 3 => [null, null, 0, null, null];
        yield 4 => [null, null, 0, 0, null];
        yield 5 => [null, null, 0, -1, null];
        yield 6 => [null, null, 0, 1, null];
        yield 7 => [null, null, -1, null, null];
        yield 8 => [null, null, -1, 0, null];
        yield 9 => [null, null, -1, -1, null];
        yield 10 => [null, null, -1, 1, null];
        yield 11 => [null, null, 1, null, null];
        yield 12 => [null, null, 1, 0, null];
        yield 13 => [null, null, 1, -1, null];
        yield 14 => [null, null, 1, 1, null];
        yield 15 => [null, 100, null, null, null];
        yield 16 => [null, 100, null, null, '<'];
        yield 17 => [null, 100, null, null, '!'];
        yield 18 => [null, 100, null, null, '^'];
        yield 19 => [null, 100, null, null, '>'];
        yield 20 => [null, 100, null, 0, null];
        yield 21 => [null, 100, null, 0, '<'];
        yield 22 => [null, 100, null, 0, '!'];
        yield 23 => [null, 100, null, 0, '^'];
        yield 24 => [null, 100, null, 0, '>'];
        yield 25 => [null, 100, null, -1, null];
        yield 26 => [null, 100, null, -1, '<'];
        yield 27 => [null, 100, null, -1, '!'];
        yield 28 => [null, 100, null, -1, '^'];
        yield 29 => [null, 100, null, -1, '>'];
        yield 30 => [null, 100, null, 1, null];
        yield 31 => [null, 100, null, 1, '<'];
        yield 32 => [null, 100, null, 1, '!'];
        yield 33 => [null, 100, null, 1, '^'];
        yield 34 => [null, 100, null, 1, '>'];
        yield 35 => [null, 100, 0, null, null];
        yield 36 => [null, 100, 0, null, '<'];
        yield 37 => [null, 100, 0, null, '!'];
        yield 38 => [null, 100, 0, null, '^'];
        yield 39 => [null, 100, 0, null, '>'];
        yield 40 => [null, 100, 0, 0, null];
        yield 41 => [null, 100, 0, 0, '<'];
        yield 42 => [null, 100, 0, 0, '!'];
        yield 43 => [null, 100, 0, 0, '^'];
        yield 44 => [null, 100, 0, 0, '>'];
        yield 45 => [null, 100, 0, -1, null];
        yield 46 => [null, 100, 0, -1, '<'];
        yield 47 => [null, 100, 0, -1, '!'];
        yield 48 => [null, 100, 0, -1, '^'];
        yield 49 => [null, 100, 0, -1, '>'];
        yield 50 => [null, 100, 0, 1, null];
        yield 51 => [null, 100, 0, 1, '<'];
        yield 52 => [null, 100, 0, 1, '!'];
        yield 53 => [null, 100, 0, 1, '^'];
        yield 54 => [null, 100, 0, 1, '>'];
        yield 55 => [null, 100, -1, null, null];
        yield 56 => [null, 100, -1, null, '<'];
        yield 57 => [null, 100, -1, null, '!'];
        yield 58 => [null, 100, -1, null, '^'];
        yield 59 => [null, 100, -1, null, '>'];
        yield 60 => [null, 100, -1, 0, null];
        yield 61 => [null, 100, -1, 0, '<'];
        yield 62 => [null, 100, -1, 0, '!'];
        yield 63 => [null, 100, -1, 0, '^'];
        yield 64 => [null, 100, -1, 0, '>'];
        yield 65 => [null, 100, -1, -1, null];
        yield 66 => [null, 100, -1, -1, '<'];
        yield 67 => [null, 100, -1, -1, '!'];
        yield 68 => [null, 100, -1, -1, '^'];
        yield 69 => [null, 100, -1, -1, '>'];
        yield 70 => [null, 100, -1, 1, null];
        yield 71 => [null, 100, -1, 1, '<'];
        yield 72 => [null, 100, -1, 1, '!'];
        yield 73 => [null, 100, -1, 1, '^'];
        yield 74 => [null, 100, -1, 1, '>'];
        yield 75 => [null, 100, 1, null, null];
        yield 76 => [null, 100, 1, null, '<'];
        yield 77 => [null, 100, 1, null, '!'];
        yield 78 => [null, 100, 1, null, '^'];
        yield 79 => [null, 100, 1, null, '>'];
        yield 80 => [null, 100, 1, 0, null];
        yield 81 => [null, 100, 1, 0, '<'];
        yield 82 => [null, 100, 1, 0, '!'];
        yield 83 => [null, 100, 1, 0, '^'];
        yield 84 => [null, 100, 1, 0, '>'];
        yield 85 => [null, 100, 1, -1, null];
        yield 86 => [null, 100, 1, -1, '<'];
        yield 87 => [null, 100, 1, -1, '!'];
        yield 88 => [null, 100, 1, -1, '^'];
        yield 89 => [null, 100, 1, -1, '>'];
        yield 90 => [null, 100, 1, 1, null];
        yield 91 => [null, 100, 1, 1, '<'];
        yield 92 => [null, 100, 1, 1, '!'];
        yield 93 => [null, 100, 1, 1, '^'];
        yield 94 => [null, 100, 1, 1, '>'];
        yield 95 => [100, null, null, null, null];
        yield 96 => [100, null, null, null, '<'];
        yield 97 => [100, null, null, null, '!'];
        yield 98 => [100, null, null, null, '^'];
        yield 99 => [100, null, null, null, '>'];
        yield 100 => [100, null, null, 0, null];
        yield 101 => [100, null, null, 0, '<'];
        yield 102 => [100, null, null, 0, '!'];
        yield 103 => [100, null, null, 0, '^'];
        yield 104 => [100, null, null, 0, '>'];
        yield 105 => [100, null, null, -1, null];
        yield 106 => [100, null, null, -1, '<'];
        yield 107 => [100, null, null, -1, '!'];
        yield 108 => [100, null, null, -1, '^'];
        yield 109 => [100, null, null, -1, '>'];
        yield 110 => [100, null, null, 1, null];
        yield 111 => [100, null, null, 1, '<'];
        yield 112 => [100, null, null, 1, '!'];
        yield 113 => [100, null, null, 1, '^'];
        yield 114 => [100, null, null, 1, '>'];
        yield 115 => [100, null, 0, null, null];
        yield 116 => [100, null, 0, null, '<'];
        yield 117 => [100, null, 0, null, '!'];
        yield 118 => [100, null, 0, null, '^'];
        yield 119 => [100, null, 0, null, '>'];
        yield 120 => [100, null, 0, 0, null];
        yield 121 => [100, null, 0, 0, '<'];
        yield 122 => [100, null, 0, 0, '!'];
        yield 123 => [100, null, 0, 0, '^'];
        yield 124 => [100, null, 0, 0, '>'];
        yield 125 => [100, null, 0, -1, null];
        yield 126 => [100, null, 0, -1, '<'];
        yield 127 => [100, null, 0, -1, '!'];
        yield 128 => [100, null, 0, -1, '^'];
        yield 129 => [100, null, 0, -1, '>'];
        yield 130 => [100, null, 0, 1, null];
        yield 131 => [100, null, 0, 1, '<'];
        yield 132 => [100, null, 0, 1, '!'];
        yield 133 => [100, null, 0, 1, '^'];
        yield 134 => [100, null, 0, 1, '>'];
        yield 135 => [100, null, -1, null, null];
        yield 136 => [100, null, -1, null, '<'];
        yield 137 => [100, null, -1, null, '!'];
        yield 138 => [100, null, -1, null, '^'];
        yield 139 => [100, null, -1, null, '>'];
        yield 140 => [100, null, -1, 0, null];
        yield 141 => [100, null, -1, 0, '<'];
        yield 142 => [100, null, -1, 0, '!'];
        yield 143 => [100, null, -1, 0, '^'];
        yield 144 => [100, null, -1, 0, '>'];
        yield 145 => [100, null, -1, -1, null];
        yield 146 => [100, null, -1, -1, '<'];
        yield 147 => [100, null, -1, -1, '!'];
        yield 148 => [100, null, -1, -1, '^'];
        yield 149 => [100, null, -1, -1, '>'];
        yield 150 => [100, null, -1, 1, null];
        yield 151 => [100, null, -1, 1, '<'];
        yield 152 => [100, null, -1, 1, '!'];
        yield 153 => [100, null, -1, 1, '^'];
        yield 154 => [100, null, -1, 1, '>'];
        yield 155 => [100, null, 1, null, null];
        yield 156 => [100, null, 1, null, '<'];
        yield 157 => [100, null, 1, null, '!'];
        yield 158 => [100, null, 1, null, '^'];
        yield 159 => [100, null, 1, null, '>'];
        yield 160 => [100, null, 1, 0, null];
        yield 161 => [100, null, 1, 0, '<'];
        yield 162 => [100, null, 1, 0, '!'];
        yield 163 => [100, null, 1, 0, '^'];
        yield 164 => [100, null, 1, 0, '>'];
        yield 165 => [100, null, 1, -1, null];
        yield 166 => [100, null, 1, -1, '<'];
        yield 167 => [100, null, 1, -1, '!'];
        yield 168 => [100, null, 1, -1, '^'];
        yield 169 => [100, null, 1, -1, '>'];
        yield 170 => [100, null, 1, 1, null];
        yield 171 => [100, null, 1, 1, '<'];
        yield 172 => [100, null, 1, 1, '!'];
        yield 173 => [100, null, 1, 1, '^'];
        yield 174 => [100, null, 1, 1, '>'];
        yield 175 => [100, 100, null, null, null];
        yield 176 => [100, 100, null, null, '<'];
        yield 177 => [100, 100, null, null, '!'];
        yield 178 => [100, 100, null, null, '^'];
        yield 179 => [100, 100, null, null, '>'];
        yield 180 => [100, 100, null, 0, null];
        yield 181 => [100, 100, null, 0, '<'];
        yield 182 => [100, 100, null, 0, '!'];
        yield 183 => [100, 100, null, 0, '^'];
        yield 184 => [100, 100, null, 0, '>'];
        yield 185 => [100, 100, null, -1, null];
        yield 186 => [100, 100, null, -1, '<'];
        yield 187 => [100, 100, null, -1, '!'];
        yield 188 => [100, 100, null, -1, '^'];
        yield 189 => [100, 100, null, -1, '>'];
        yield 190 => [100, 100, null, 1, null];
        yield 191 => [100, 100, null, 1, '<'];
        yield 192 => [100, 100, null, 1, '!'];
        yield 193 => [100, 100, null, 1, '^'];
        yield 194 => [100, 100, null, 1, '>'];
        yield 195 => [100, 100, 0, null, null];
        yield 196 => [100, 100, 0, null, '<'];
        yield 197 => [100, 100, 0, null, '!'];
        yield 198 => [100, 100, 0, null, '^'];
        yield 199 => [100, 100, 0, null, '>'];
        yield 200 => [100, 100, 0, 0, null];
        yield 201 => [100, 100, 0, 0, '<'];
        yield 202 => [100, 100, 0, 0, '!'];
        yield 203 => [100, 100, 0, 0, '^'];
        yield 204 => [100, 100, 0, 0, '>'];
        yield 205 => [100, 100, 0, -1, null];
        yield 206 => [100, 100, 0, -1, '<'];
        yield 207 => [100, 100, 0, -1, '!'];
        yield 208 => [100, 100, 0, -1, '^'];
        yield 209 => [100, 100, 0, -1, '>'];
        yield 210 => [100, 100, 0, 1, null];
        yield 211 => [100, 100, 0, 1, '<'];
        yield 212 => [100, 100, 0, 1, '!'];
        yield 213 => [100, 100, 0, 1, '^'];
        yield 214 => [100, 100, 0, 1, '>'];
        yield 215 => [100, 100, -1, null, null];
        yield 216 => [100, 100, -1, null, '<'];
        yield 217 => [100, 100, -1, null, '!'];
        yield 218 => [100, 100, -1, null, '^'];
        yield 219 => [100, 100, -1, null, '>'];
        yield 220 => [100, 100, -1, 0, null];
        yield 221 => [100, 100, -1, 0, '<'];
        yield 222 => [100, 100, -1, 0, '!'];
        yield 223 => [100, 100, -1, 0, '^'];
        yield 224 => [100, 100, -1, 0, '>'];
        yield 225 => [100, 100, -1, -1, null];
        yield 226 => [100, 100, -1, -1, '<'];
        yield 227 => [100, 100, -1, -1, '!'];
        yield 228 => [100, 100, -1, -1, '^'];
        yield 229 => [100, 100, -1, -1, '>'];
        yield 230 => [100, 100, -1, 1, null];
        yield 231 => [100, 100, -1, 1, '<'];
        yield 232 => [100, 100, -1, 1, '!'];
        yield 233 => [100, 100, -1, 1, '^'];
        yield 234 => [100, 100, -1, 1, '>'];
        yield 235 => [100, 100, 1, null, null];
        yield 236 => [100, 100, 1, null, '<'];
        yield 237 => [100, 100, 1, null, '!'];
        yield 238 => [100, 100, 1, null, '^'];
        yield 239 => [100, 100, 1, null, '>'];
        yield 240 => [100, 100, 1, 0, null];
        yield 241 => [100, 100, 1, 0, '<'];
        yield 242 => [100, 100, 1, 0, '!'];
        yield 243 => [100, 100, 1, 0, '^'];
        yield 244 => [100, 100, 1, 0, '>'];
        yield 245 => [100, 100, 1, -1, null];
        yield 246 => [100, 100, 1, -1, '<'];
        yield 247 => [100, 100, 1, -1, '!'];
        yield 248 => [100, 100, 1, -1, '^'];
        yield 249 => [100, 100, 1, -1, '>'];
        yield 250 => [100, 100, 1, 1, null];
        yield 251 => [100, 100, 1, 1, '<'];
        yield 252 => [100, 100, 1, 1, '!'];
        yield 253 => [100, 100, 1, 1, '^'];
        yield 254 => [100, 100, 1, 1, '>'];
    }

    /**
     * @dataProvider provideWrongGeometries
     */
    public function testWrongGeometry(?int $width, ?int $height, ?int $x, ?int $y, ?string $aspectRatio): void
    {
        $geometry = new Geometry($width, $height, $x, $y, $aspectRatio);

        $expectedGeometry = Geometry::createFromParameters($width, $height, $x, $y, $aspectRatio);

        $message = null;

        try {
            $geometry->validate();
        } catch (\InvalidArgumentException $e) {
            $message = $e->getMessage();
        }

        static::assertNotNull($message, 'No exception for geometry "'.$expectedGeometry.'"');
        static::assertStringStartsWith('The specified geometry ('.$expectedGeometry.') is invalid.', $message, "Wrong exception message:\n$message");
    }

    public function provideWrongGeometries(): ?\Generator
    {
        yield 0 => [null, null, null, null, '<'];
        yield 1 => [null, null, null, null, '!'];
        yield 2 => [null, null, null, null, '^'];
        yield 3 => [null, null, null, null, '>'];
        yield 4 => [null, null, null, 0, '<'];
        yield 5 => [null, null, null, 0, '!'];
        yield 6 => [null, null, null, 0, '^'];
        yield 7 => [null, null, null, 0, '>'];
        yield 8 => [null, null, null, -1, '<'];
        yield 9 => [null, null, null, -1, '!'];
        yield 10 => [null, null, null, -1, '^'];
        yield 11 => [null, null, null, -1, '>'];
        yield 12 => [null, null, null, 1, '<'];
        yield 13 => [null, null, null, 1, '!'];
        yield 14 => [null, null, null, 1, '^'];
        yield 15 => [null, null, null, 1, '>'];
        yield 16 => [null, null, 0, null, '<'];
        yield 17 => [null, null, 0, null, '!'];
        yield 18 => [null, null, 0, null, '^'];
        yield 19 => [null, null, 0, null, '>'];
        yield 20 => [null, null, 0, 0, '<'];
        yield 21 => [null, null, 0, 0, '!'];
        yield 22 => [null, null, 0, 0, '^'];
        yield 23 => [null, null, 0, 0, '>'];
        yield 24 => [null, null, 0, -1, '<'];
        yield 25 => [null, null, 0, -1, '!'];
        yield 26 => [null, null, 0, -1, '^'];
        yield 27 => [null, null, 0, -1, '>'];
        yield 28 => [null, null, 0, 1, '<'];
        yield 29 => [null, null, 0, 1, '!'];
        yield 30 => [null, null, 0, 1, '^'];
        yield 31 => [null, null, 0, 1, '>'];
        yield 32 => [null, null, -1, null, '<'];
        yield 33 => [null, null, -1, null, '!'];
        yield 34 => [null, null, -1, null, '^'];
        yield 35 => [null, null, -1, null, '>'];
        yield 36 => [null, null, -1, 0, '<'];
        yield 37 => [null, null, -1, 0, '!'];
        yield 38 => [null, null, -1, 0, '^'];
        yield 39 => [null, null, -1, 0, '>'];
        yield 40 => [null, null, -1, -1, '<'];
        yield 41 => [null, null, -1, -1, '!'];
        yield 42 => [null, null, -1, -1, '^'];
        yield 43 => [null, null, -1, -1, '>'];
        yield 44 => [null, null, -1, 1, '<'];
        yield 45 => [null, null, -1, 1, '!'];
        yield 46 => [null, null, -1, 1, '^'];
        yield 47 => [null, null, -1, 1, '>'];
        yield 48 => [null, null, 1, null, '<'];
        yield 49 => [null, null, 1, null, '!'];
        yield 50 => [null, null, 1, null, '^'];
        yield 51 => [null, null, 1, null, '>'];
        yield 52 => [null, null, 1, 0, '<'];
        yield 53 => [null, null, 1, 0, '!'];
        yield 54 => [null, null, 1, 0, '^'];
        yield 55 => [null, null, 1, 0, '>'];
        yield 56 => [null, null, 1, -1, '<'];
        yield 57 => [null, null, 1, -1, '!'];
        yield 58 => [null, null, 1, -1, '^'];
        yield 59 => [null, null, 1, -1, '>'];
        yield 60 => [null, null, 1, 1, '<'];
        yield 61 => [null, null, 1, 1, '!'];
        yield 62 => [null, null, 1, 1, '^'];
        yield 63 => [null, null, 1, 1, '>'];
        yield 64 => [null, 0, null, null, null];
        yield 65 => [null, 0, null, null, '<'];
        yield 66 => [null, 0, null, null, '!'];
        yield 67 => [null, 0, null, null, '^'];
        yield 68 => [null, 0, null, null, '>'];
        yield 69 => [null, 0, null, 0, null];
        yield 70 => [null, 0, null, 0, '<'];
        yield 71 => [null, 0, null, 0, '!'];
        yield 72 => [null, 0, null, 0, '^'];
        yield 73 => [null, 0, null, 0, '>'];
        yield 74 => [null, 0, null, -1, null];
        yield 75 => [null, 0, null, -1, '<'];
        yield 76 => [null, 0, null, -1, '!'];
        yield 77 => [null, 0, null, -1, '^'];
        yield 78 => [null, 0, null, -1, '>'];
        yield 79 => [null, 0, null, 1, null];
        yield 80 => [null, 0, null, 1, '<'];
        yield 81 => [null, 0, null, 1, '!'];
        yield 82 => [null, 0, null, 1, '^'];
        yield 83 => [null, 0, null, 1, '>'];
        yield 84 => [null, 0, 0, null, null];
        yield 85 => [null, 0, 0, null, '<'];
        yield 86 => [null, 0, 0, null, '!'];
        yield 87 => [null, 0, 0, null, '^'];
        yield 88 => [null, 0, 0, null, '>'];
        yield 89 => [null, 0, 0, 0, null];
        yield 90 => [null, 0, 0, 0, '<'];
        yield 91 => [null, 0, 0, 0, '!'];
        yield 92 => [null, 0, 0, 0, '^'];
        yield 93 => [null, 0, 0, 0, '>'];
        yield 94 => [null, 0, 0, -1, null];
        yield 95 => [null, 0, 0, -1, '<'];
        yield 96 => [null, 0, 0, -1, '!'];
        yield 97 => [null, 0, 0, -1, '^'];
        yield 98 => [null, 0, 0, -1, '>'];
        yield 99 => [null, 0, 0, 1, null];
        yield 100 => [null, 0, 0, 1, '<'];
        yield 101 => [null, 0, 0, 1, '!'];
        yield 102 => [null, 0, 0, 1, '^'];
        yield 103 => [null, 0, 0, 1, '>'];
        yield 104 => [null, 0, -1, null, null];
        yield 105 => [null, 0, -1, null, '<'];
        yield 106 => [null, 0, -1, null, '!'];
        yield 107 => [null, 0, -1, null, '^'];
        yield 108 => [null, 0, -1, null, '>'];
        yield 109 => [null, 0, -1, 0, null];
        yield 110 => [null, 0, -1, 0, '<'];
        yield 111 => [null, 0, -1, 0, '!'];
        yield 112 => [null, 0, -1, 0, '^'];
        yield 113 => [null, 0, -1, 0, '>'];
        yield 114 => [null, 0, -1, -1, null];
        yield 115 => [null, 0, -1, -1, '<'];
        yield 116 => [null, 0, -1, -1, '!'];
        yield 117 => [null, 0, -1, -1, '^'];
        yield 118 => [null, 0, -1, -1, '>'];
        yield 119 => [null, 0, -1, 1, null];
        yield 120 => [null, 0, -1, 1, '<'];
        yield 121 => [null, 0, -1, 1, '!'];
        yield 122 => [null, 0, -1, 1, '^'];
        yield 123 => [null, 0, -1, 1, '>'];
        yield 124 => [null, 0, 1, null, null];
        yield 125 => [null, 0, 1, null, '<'];
        yield 126 => [null, 0, 1, null, '!'];
        yield 127 => [null, 0, 1, null, '^'];
        yield 128 => [null, 0, 1, null, '>'];
        yield 129 => [null, 0, 1, 0, null];
        yield 130 => [null, 0, 1, 0, '<'];
        yield 131 => [null, 0, 1, 0, '!'];
        yield 132 => [null, 0, 1, 0, '^'];
        yield 133 => [null, 0, 1, 0, '>'];
        yield 134 => [null, 0, 1, -1, null];
        yield 135 => [null, 0, 1, -1, '<'];
        yield 136 => [null, 0, 1, -1, '!'];
        yield 137 => [null, 0, 1, -1, '^'];
        yield 138 => [null, 0, 1, -1, '>'];
        yield 139 => [null, 0, 1, 1, null];
        yield 140 => [null, 0, 1, 1, '<'];
        yield 141 => [null, 0, 1, 1, '!'];
        yield 142 => [null, 0, 1, 1, '^'];
        yield 143 => [null, 0, 1, 1, '>'];
        yield 144 => [0, null, null, null, null];
        yield 145 => [0, null, null, null, '<'];
        yield 146 => [0, null, null, null, '!'];
        yield 147 => [0, null, null, null, '^'];
        yield 148 => [0, null, null, null, '>'];
        yield 149 => [0, null, null, 0, null];
        yield 150 => [0, null, null, 0, '<'];
        yield 151 => [0, null, null, 0, '!'];
        yield 152 => [0, null, null, 0, '^'];
        yield 153 => [0, null, null, 0, '>'];
        yield 154 => [0, null, null, -1, null];
        yield 155 => [0, null, null, -1, '<'];
        yield 156 => [0, null, null, -1, '!'];
        yield 157 => [0, null, null, -1, '^'];
        yield 158 => [0, null, null, -1, '>'];
        yield 159 => [0, null, null, 1, null];
        yield 160 => [0, null, null, 1, '<'];
        yield 161 => [0, null, null, 1, '!'];
        yield 162 => [0, null, null, 1, '^'];
        yield 163 => [0, null, null, 1, '>'];
        yield 164 => [0, null, 0, null, null];
        yield 165 => [0, null, 0, null, '<'];
        yield 166 => [0, null, 0, null, '!'];
        yield 167 => [0, null, 0, null, '^'];
        yield 168 => [0, null, 0, null, '>'];
        yield 169 => [0, null, 0, 0, null];
        yield 170 => [0, null, 0, 0, '<'];
        yield 171 => [0, null, 0, 0, '!'];
        yield 172 => [0, null, 0, 0, '^'];
        yield 173 => [0, null, 0, 0, '>'];
        yield 174 => [0, null, 0, -1, null];
        yield 175 => [0, null, 0, -1, '<'];
        yield 176 => [0, null, 0, -1, '!'];
        yield 177 => [0, null, 0, -1, '^'];
        yield 178 => [0, null, 0, -1, '>'];
        yield 179 => [0, null, 0, 1, null];
        yield 180 => [0, null, 0, 1, '<'];
        yield 181 => [0, null, 0, 1, '!'];
        yield 182 => [0, null, 0, 1, '^'];
        yield 183 => [0, null, 0, 1, '>'];
        yield 184 => [0, null, -1, null, null];
        yield 185 => [0, null, -1, null, '<'];
        yield 186 => [0, null, -1, null, '!'];
        yield 187 => [0, null, -1, null, '^'];
        yield 188 => [0, null, -1, null, '>'];
        yield 189 => [0, null, -1, 0, null];
        yield 190 => [0, null, -1, 0, '<'];
        yield 191 => [0, null, -1, 0, '!'];
        yield 192 => [0, null, -1, 0, '^'];
        yield 193 => [0, null, -1, 0, '>'];
        yield 194 => [0, null, -1, -1, null];
        yield 195 => [0, null, -1, -1, '<'];
        yield 196 => [0, null, -1, -1, '!'];
        yield 197 => [0, null, -1, -1, '^'];
        yield 198 => [0, null, -1, -1, '>'];
        yield 199 => [0, null, -1, 1, null];
        yield 200 => [0, null, -1, 1, '<'];
        yield 201 => [0, null, -1, 1, '!'];
        yield 202 => [0, null, -1, 1, '^'];
        yield 203 => [0, null, -1, 1, '>'];
        yield 204 => [0, null, 1, null, null];
        yield 205 => [0, null, 1, null, '<'];
        yield 206 => [0, null, 1, null, '!'];
        yield 207 => [0, null, 1, null, '^'];
        yield 208 => [0, null, 1, null, '>'];
        yield 209 => [0, null, 1, 0, null];
        yield 210 => [0, null, 1, 0, '<'];
        yield 211 => [0, null, 1, 0, '!'];
        yield 212 => [0, null, 1, 0, '^'];
        yield 213 => [0, null, 1, 0, '>'];
        yield 214 => [0, null, 1, -1, null];
        yield 215 => [0, null, 1, -1, '<'];
        yield 216 => [0, null, 1, -1, '!'];
        yield 217 => [0, null, 1, -1, '^'];
        yield 218 => [0, null, 1, -1, '>'];
        yield 219 => [0, null, 1, 1, null];
        yield 220 => [0, null, 1, 1, '<'];
        yield 221 => [0, null, 1, 1, '!'];
        yield 222 => [0, null, 1, 1, '^'];
        yield 223 => [0, null, 1, 1, '>'];
        yield 224 => [0, 0, null, null, null];
        yield 225 => [0, 0, null, null, '<'];
        yield 226 => [0, 0, null, null, '!'];
        yield 227 => [0, 0, null, null, '^'];
        yield 228 => [0, 0, null, null, '>'];
        yield 229 => [0, 0, null, 0, null];
        yield 230 => [0, 0, null, 0, '<'];
        yield 231 => [0, 0, null, 0, '!'];
        yield 232 => [0, 0, null, 0, '^'];
        yield 233 => [0, 0, null, 0, '>'];
        yield 234 => [0, 0, null, -1, null];
        yield 235 => [0, 0, null, -1, '<'];
        yield 236 => [0, 0, null, -1, '!'];
        yield 237 => [0, 0, null, -1, '^'];
        yield 238 => [0, 0, null, -1, '>'];
        yield 239 => [0, 0, null, 1, null];
        yield 240 => [0, 0, null, 1, '<'];
        yield 241 => [0, 0, null, 1, '!'];
        yield 242 => [0, 0, null, 1, '^'];
        yield 243 => [0, 0, null, 1, '>'];
        yield 244 => [0, 0, 0, null, null];
        yield 245 => [0, 0, 0, null, '<'];
        yield 246 => [0, 0, 0, null, '!'];
        yield 247 => [0, 0, 0, null, '^'];
        yield 248 => [0, 0, 0, null, '>'];
        yield 249 => [0, 0, 0, 0, null];
        yield 250 => [0, 0, 0, 0, '<'];
        yield 251 => [0, 0, 0, 0, '!'];
        yield 252 => [0, 0, 0, 0, '^'];
        yield 253 => [0, 0, 0, 0, '>'];
        yield 254 => [0, 0, 0, -1, null];
        yield 255 => [0, 0, 0, -1, '<'];
        yield 256 => [0, 0, 0, -1, '!'];
        yield 257 => [0, 0, 0, -1, '^'];
        yield 258 => [0, 0, 0, -1, '>'];
        yield 259 => [0, 0, 0, 1, null];
        yield 260 => [0, 0, 0, 1, '<'];
        yield 261 => [0, 0, 0, 1, '!'];
        yield 262 => [0, 0, 0, 1, '^'];
        yield 263 => [0, 0, 0, 1, '>'];
        yield 264 => [0, 0, -1, null, null];
        yield 265 => [0, 0, -1, null, '<'];
        yield 266 => [0, 0, -1, null, '!'];
        yield 267 => [0, 0, -1, null, '^'];
        yield 268 => [0, 0, -1, null, '>'];
        yield 269 => [0, 0, -1, 0, null];
        yield 270 => [0, 0, -1, 0, '<'];
        yield 271 => [0, 0, -1, 0, '!'];
        yield 272 => [0, 0, -1, 0, '^'];
        yield 273 => [0, 0, -1, 0, '>'];
        yield 274 => [0, 0, -1, -1, null];
        yield 275 => [0, 0, -1, -1, '<'];
        yield 276 => [0, 0, -1, -1, '!'];
        yield 277 => [0, 0, -1, -1, '^'];
        yield 278 => [0, 0, -1, -1, '>'];
        yield 279 => [0, 0, -1, 1, null];
        yield 280 => [0, 0, -1, 1, '<'];
        yield 281 => [0, 0, -1, 1, '!'];
        yield 282 => [0, 0, -1, 1, '^'];
        yield 283 => [0, 0, -1, 1, '>'];
        yield 284 => [0, 0, 1, null, null];
        yield 285 => [0, 0, 1, null, '<'];
        yield 286 => [0, 0, 1, null, '!'];
        yield 287 => [0, 0, 1, null, '^'];
        yield 288 => [0, 0, 1, null, '>'];
        yield 289 => [0, 0, 1, 0, null];
        yield 290 => [0, 0, 1, 0, '<'];
        yield 291 => [0, 0, 1, 0, '!'];
        yield 292 => [0, 0, 1, 0, '^'];
        yield 293 => [0, 0, 1, 0, '>'];
        yield 294 => [0, 0, 1, -1, null];
        yield 295 => [0, 0, 1, -1, '<'];
        yield 296 => [0, 0, 1, -1, '!'];
        yield 297 => [0, 0, 1, -1, '^'];
        yield 298 => [0, 0, 1, -1, '>'];
        yield 299 => [0, 0, 1, 1, null];
        yield 300 => [0, 0, 1, 1, '<'];
        yield 301 => [0, 0, 1, 1, '!'];
        yield 302 => [0, 0, 1, 1, '^'];
        yield 303 => [0, 0, 1, 1, '>'];
        yield 304 => [0, 100, null, null, null];
        yield 305 => [0, 100, null, null, '<'];
        yield 306 => [0, 100, null, null, '!'];
        yield 307 => [0, 100, null, null, '^'];
        yield 308 => [0, 100, null, null, '>'];
        yield 309 => [0, 100, null, 0, null];
        yield 310 => [0, 100, null, 0, '<'];
        yield 311 => [0, 100, null, 0, '!'];
        yield 312 => [0, 100, null, 0, '^'];
        yield 313 => [0, 100, null, 0, '>'];
        yield 314 => [0, 100, null, -1, null];
        yield 315 => [0, 100, null, -1, '<'];
        yield 316 => [0, 100, null, -1, '!'];
        yield 317 => [0, 100, null, -1, '^'];
        yield 318 => [0, 100, null, -1, '>'];
        yield 319 => [0, 100, null, 1, null];
        yield 320 => [0, 100, null, 1, '<'];
        yield 321 => [0, 100, null, 1, '!'];
        yield 322 => [0, 100, null, 1, '^'];
        yield 323 => [0, 100, null, 1, '>'];
        yield 324 => [0, 100, 0, null, null];
        yield 325 => [0, 100, 0, null, '<'];
        yield 326 => [0, 100, 0, null, '!'];
        yield 327 => [0, 100, 0, null, '^'];
        yield 328 => [0, 100, 0, null, '>'];
        yield 329 => [0, 100, 0, 0, null];
        yield 330 => [0, 100, 0, 0, '<'];
        yield 331 => [0, 100, 0, 0, '!'];
        yield 332 => [0, 100, 0, 0, '^'];
        yield 333 => [0, 100, 0, 0, '>'];
        yield 334 => [0, 100, 0, -1, null];
        yield 335 => [0, 100, 0, -1, '<'];
        yield 336 => [0, 100, 0, -1, '!'];
        yield 337 => [0, 100, 0, -1, '^'];
        yield 338 => [0, 100, 0, -1, '>'];
        yield 339 => [0, 100, 0, 1, null];
        yield 340 => [0, 100, 0, 1, '<'];
        yield 341 => [0, 100, 0, 1, '!'];
        yield 342 => [0, 100, 0, 1, '^'];
        yield 343 => [0, 100, 0, 1, '>'];
        yield 344 => [0, 100, -1, null, null];
        yield 345 => [0, 100, -1, null, '<'];
        yield 346 => [0, 100, -1, null, '!'];
        yield 347 => [0, 100, -1, null, '^'];
        yield 348 => [0, 100, -1, null, '>'];
        yield 349 => [0, 100, -1, 0, null];
        yield 350 => [0, 100, -1, 0, '<'];
        yield 351 => [0, 100, -1, 0, '!'];
        yield 352 => [0, 100, -1, 0, '^'];
        yield 353 => [0, 100, -1, 0, '>'];
        yield 354 => [0, 100, -1, -1, null];
        yield 355 => [0, 100, -1, -1, '<'];
        yield 356 => [0, 100, -1, -1, '!'];
        yield 357 => [0, 100, -1, -1, '^'];
        yield 358 => [0, 100, -1, -1, '>'];
        yield 359 => [0, 100, -1, 1, null];
        yield 360 => [0, 100, -1, 1, '<'];
        yield 361 => [0, 100, -1, 1, '!'];
        yield 362 => [0, 100, -1, 1, '^'];
        yield 363 => [0, 100, -1, 1, '>'];
        yield 364 => [0, 100, 1, null, null];
        yield 365 => [0, 100, 1, null, '<'];
        yield 366 => [0, 100, 1, null, '!'];
        yield 367 => [0, 100, 1, null, '^'];
        yield 368 => [0, 100, 1, null, '>'];
        yield 369 => [0, 100, 1, 0, null];
        yield 370 => [0, 100, 1, 0, '<'];
        yield 371 => [0, 100, 1, 0, '!'];
        yield 372 => [0, 100, 1, 0, '^'];
        yield 373 => [0, 100, 1, 0, '>'];
        yield 374 => [0, 100, 1, -1, null];
        yield 375 => [0, 100, 1, -1, '<'];
        yield 376 => [0, 100, 1, -1, '!'];
        yield 377 => [0, 100, 1, -1, '^'];
        yield 378 => [0, 100, 1, -1, '>'];
        yield 379 => [0, 100, 1, 1, null];
        yield 380 => [0, 100, 1, 1, '<'];
        yield 381 => [0, 100, 1, 1, '!'];
        yield 382 => [0, 100, 1, 1, '^'];
        yield 383 => [0, 100, 1, 1, '>'];
        yield 384 => [100, 0, null, null, null];
        yield 385 => [100, 0, null, null, '<'];
        yield 386 => [100, 0, null, null, '!'];
        yield 387 => [100, 0, null, null, '^'];
        yield 388 => [100, 0, null, null, '>'];
        yield 389 => [100, 0, null, 0, null];
        yield 390 => [100, 0, null, 0, '<'];
        yield 391 => [100, 0, null, 0, '!'];
        yield 392 => [100, 0, null, 0, '^'];
        yield 393 => [100, 0, null, 0, '>'];
        yield 394 => [100, 0, null, -1, null];
        yield 395 => [100, 0, null, -1, '<'];
        yield 396 => [100, 0, null, -1, '!'];
        yield 397 => [100, 0, null, -1, '^'];
        yield 398 => [100, 0, null, -1, '>'];
        yield 399 => [100, 0, null, 1, null];
        yield 400 => [100, 0, null, 1, '<'];
        yield 401 => [100, 0, null, 1, '!'];
        yield 402 => [100, 0, null, 1, '^'];
        yield 403 => [100, 0, null, 1, '>'];
        yield 404 => [100, 0, 0, null, null];
        yield 405 => [100, 0, 0, null, '<'];
        yield 406 => [100, 0, 0, null, '!'];
        yield 407 => [100, 0, 0, null, '^'];
        yield 408 => [100, 0, 0, null, '>'];
        yield 409 => [100, 0, 0, 0, null];
        yield 410 => [100, 0, 0, 0, '<'];
        yield 411 => [100, 0, 0, 0, '!'];
        yield 412 => [100, 0, 0, 0, '^'];
        yield 413 => [100, 0, 0, 0, '>'];
        yield 414 => [100, 0, 0, -1, null];
        yield 415 => [100, 0, 0, -1, '<'];
        yield 416 => [100, 0, 0, -1, '!'];
        yield 417 => [100, 0, 0, -1, '^'];
        yield 418 => [100, 0, 0, -1, '>'];
        yield 419 => [100, 0, 0, 1, null];
        yield 420 => [100, 0, 0, 1, '<'];
        yield 421 => [100, 0, 0, 1, '!'];
        yield 422 => [100, 0, 0, 1, '^'];
        yield 423 => [100, 0, 0, 1, '>'];
        yield 424 => [100, 0, -1, null, null];
        yield 425 => [100, 0, -1, null, '<'];
        yield 426 => [100, 0, -1, null, '!'];
        yield 427 => [100, 0, -1, null, '^'];
        yield 428 => [100, 0, -1, null, '>'];
        yield 429 => [100, 0, -1, 0, null];
        yield 430 => [100, 0, -1, 0, '<'];
        yield 431 => [100, 0, -1, 0, '!'];
        yield 432 => [100, 0, -1, 0, '^'];
        yield 433 => [100, 0, -1, 0, '>'];
        yield 434 => [100, 0, -1, -1, null];
        yield 435 => [100, 0, -1, -1, '<'];
        yield 436 => [100, 0, -1, -1, '!'];
        yield 437 => [100, 0, -1, -1, '^'];
        yield 438 => [100, 0, -1, -1, '>'];
        yield 439 => [100, 0, -1, 1, null];
        yield 440 => [100, 0, -1, 1, '<'];
        yield 441 => [100, 0, -1, 1, '!'];
        yield 442 => [100, 0, -1, 1, '^'];
        yield 443 => [100, 0, -1, 1, '>'];
        yield 444 => [100, 0, 1, null, null];
        yield 445 => [100, 0, 1, null, '<'];
        yield 446 => [100, 0, 1, null, '!'];
        yield 447 => [100, 0, 1, null, '^'];
        yield 448 => [100, 0, 1, null, '>'];
        yield 449 => [100, 0, 1, 0, null];
        yield 450 => [100, 0, 1, 0, '<'];
        yield 451 => [100, 0, 1, 0, '!'];
        yield 452 => [100, 0, 1, 0, '^'];
        yield 453 => [100, 0, 1, 0, '>'];
        yield 454 => [100, 0, 1, -1, null];
        yield 455 => [100, 0, 1, -1, '<'];
        yield 456 => [100, 0, 1, -1, '!'];
        yield 457 => [100, 0, 1, -1, '^'];
        yield 458 => [100, 0, 1, -1, '>'];
        yield 459 => [100, 0, 1, 1, null];
        yield 460 => [100, 0, 1, 1, '<'];
        yield 461 => [100, 0, 1, 1, '!'];
        yield 462 => [100, 0, 1, 1, '^'];
        yield 463 => [100, 0, 1, 1, '>'];
    }

    public function testInvalidAspectRatio(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf("Invalid aspect ratio value to generate geometry, \"%s\" given.\nAvailable: <, !, ^, >", 'invalid_ratio'));

        new Geometry(null, null, null, null, 'invalid_ratio');
    }

    public function testInvalidWidthHeightSeparator(): void
    {
        $geometryString = '120+1+1';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            "The specified geometry (%s) is invalid.\n%s\n"."Please refer to ImageMagick command line documentation about geometry:\nhttp://www.imagemagick.org/script/command-line-processing.php#geometry\n",
            '120+1+1',
            'When using offsets and only width, you must specify the "x" separator like this: 120x+1+1'
        ));

        $geometry = new Geometry($geometryString);
        $geometry->validate();
    }
}
