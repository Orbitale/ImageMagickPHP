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

namespace Orbitale\Component\ImageMagick\Tests;

use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase
{
    protected $resourcesDir;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        if (!\defined('IMAGEMAGICK_DIR') || !\defined('TEST_RESOURCES_DIR')) {
            throw new \RuntimeException("The \"IMAGEMAGICK_DIR\" constant is not defined.\n".'The bootstrap must be correctly included before executing test suite.');
        }
        $this->resourcesDir = TEST_RESOURCES_DIR;
    }

    protected function setUp(): void
    {
        $dir = TEST_RESOURCES_DIR.'/outputs';
        foreach (\scandir($dir, \SCANDIR_SORT_NONE) as $file) {
            if ('.' !== $file && '..' !== $file && '.gitkeep' !== $file) {
                \unlink($dir.'/'.$file);
            }
        }
    }
}
