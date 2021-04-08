<?php

/*
 * This file is part of the OrbitaleImageMagickPHP package.
 *
 * (c) Alexandre Rock Ancelet <alex@orbitale.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Orbitale\Component\ImageMagick\Command;
use Orbitale\Component\ImageMagick\MagickBinaryNotFoundException;

$file = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}
$autoload = require $file;

define('TEST_RESOURCES_DIR', __DIR__.'/Resources');

// Remove potential older output files
foreach (glob(TEST_RESOURCES_DIR.'/outputs/*', GLOB_NOSORT) as $file) {
    unlink($file);
}

// Check if ImageMagick is installed. Instead, we cannot run tests suite.
$possibleDirectories = [
    getenv('IMAGEMAGICK_PATH') ?: null,
    null,// In the PATH variable, default behavior
    '/usr/bin/magick',
    '/usr/local/bin/magick',
];
foreach ($possibleDirectories as $path) {
    echo 'Check for ImageMagick with path "'.$path."\"\n";
    try {
        $path = Command::findMagickBinaryPath($path);
        exec($path.' -version', $o, $code);
        if (0 === $code) {
            define('IMAGEMAGICK_DIR', $path);
            break;
        }
    } catch (MagickBinaryNotFoundException $e) {
        echo 'Did not find ImageMagick with path "'.$path.'". '.$e->getMessage()."\n";
    }
}

if (!defined('IMAGEMAGICK_DIR')) {
    throw new RuntimeException(
        "Couldn't locate ImageMagick.\n" .
        "Please check that ImageMagick is installed and that it is located\n" .
        'in the global PATH variable, or that it is accessible in /usr/bin'
    );
}

echo 'ImageMagick resolved to: "'.IMAGEMAGICK_DIR."\"\n";
system(IMAGEMAGICK_DIR.' -version');
