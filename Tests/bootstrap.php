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
$autoload = require_once $file;

define('TEST_RESOURCES_DIR', __DIR__.'/Resources');

// Remove potential older output files
foreach (glob(TEST_RESOURCES_DIR.'/outputs/*') as $file) {
    unlink($file);
}

// Check if ImageMagick is installed. Instead, we cannot run tests suite.
$possibleDirectories = [
    null,// In the PATH variable, default behavior
    '/usr/bin/magick',
    '/usr/local/bin/magick',
    getenv('IMAGEMAGICK_PATH') ?: null, // Fall back again to PATH
];
foreach ($possibleDirectories as $path) {
    if (!$path) {
        // Could happen if "getenv()" returns false or empty string.
        continue;
    }
    echo 'Check "'.$path.'" binary'."\n";
    try {
        $path = Command::findMagickBinaryPath($path);
        exec($path.' -version', $o, $code);
        if (0 === $code) {
            define('IMAGEMAGICK_DIR', $path);
            break;
        }
    } catch (MagickBinaryNotFoundException $e) {
    }
}

if (!defined('IMAGEMAGICK_DIR')) {
    throw new RuntimeException(
        "Couldn't locate ImageMagick.\n" .
        "Please check that ImageMagick is installed and that it is located\n" .
        'in the global PATH variable, or that it is accessible in /usr/bin'
    );
}

echo 'Analyzed ImageMagick directory: '.IMAGEMAGICK_DIR."\n";
system(IMAGEMAGICK_DIR.' -version');
