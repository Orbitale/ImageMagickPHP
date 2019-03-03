<?php

/*
 * This file is part of the OrbitaleImageMagickPHP package.
 *
 * (c) Alexandre Rock Ancelet <alex@orbitale.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    getenv('IMAGEMAGICK_PATH'),
    'magick',// In the PATH variable
    '/usr/bin/magick',
    '/usr/local/bin/magick',
];
foreach ($possibleDirectories as $dir) {
    if (!$dir) {
        // Could happen if "getenv()" returns false or empty string.
        continue;
    }
    echo 'Check "'.$dir.'" binary'."\n";
    exec($dir.' -version', $o, $code);
    if (0 === $code) {
        define('IMAGEMAGICK_DIR', $dir);
        break;
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
