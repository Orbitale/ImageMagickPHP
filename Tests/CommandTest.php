<?php
/*
* This file is part of the PierstovalImageMagickPHP package.
*
* (c) Alexandre "Pierstoval" Rock Ancelet <pierstoval@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Pierstoval\Component\ImageMagick\Tests;

use Pierstoval\Component\ImageMagick\Command;

class CommandTest extends \PHPUnit_Framework_TestCase
{

    private $resourcesDir;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->resourcesDir = realpath(dirname(__FILE__) . "../../Tests/Resources");
        if (!defined('IMAGEMAGICK_DIR')) {
            throw new \RuntimeException(
                "The \"IMAGEMAGICK_DIR\" constant is not defined.\n" .
                "The bootstrap must be correctly included before executing test suite."
            );
        }
    }

    public function testResizeImage()
    {

        $command = new Command(IMAGEMAGICK_DIR);

        $this->assertFileExists($this->resourcesDir . '/moon_180.jpg');

        $command
            ->convert($this->resourcesDir . '/moon_180.jpg')
            ->resize('100x100')
            ->file($this->resourcesDir . '/outputs/moon.jpg', false);

        $response = $command->run();

        $this->assertFileExists($this->resourcesDir . '/outputs/moon.jpg');

        $this->assertFalse($response->hasFailed());

    }
}
