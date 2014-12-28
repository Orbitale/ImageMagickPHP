<?php
/*
* This file is part of the PierstovalImageMagickPHP package.
*
* (c) Alexandre "Pierstoval" Rock Ancelet <pierstoval@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

$file = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}
$autoload = require_once $file;

// Check if ImageMagick is installed. Instead, we cannot run tests suite.

$possibleDirectories = array(
    '',// In the PATH variable
    '/usr/bin/',
    '/usr/local/bin/',
);
if (getenv('IMAGEMAGICK_DIR')) {
    array_unshift($possibleDirectories, getenv('IMAGEMAGICK_DIR'));
}
foreach ($possibleDirectories as $dir) {
    exec($dir . 'convert -version', $o, $code);
    if ($code === 0) {
        define('IMAGEMAGICK_DIR', $dir);
        break;
    }
}

if (!defined('IMAGEMAGICK_DIR')) {
    throw new \RuntimeException(
        "Couldn't locate ImageMagick.\n" .
        "Please check that ImageMagick is installed and that it is located\n" .
        "in the global PATH variable, or that it is accessible in /usr/bin"
    );
}