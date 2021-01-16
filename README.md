ImageMagickPHP
===============

[![Build Status](https://travis-ci.org/Orbitale/ImageMagickPHP.png)](https://travis-ci.org/Orbitale/ImageMagickPHP)
[![Coverage Status](https://coveralls.io/repos/Orbitale/ImageMagickPHP/badge.png)](https://coveralls.io/r/Orbitale/ImageMagickPHP)

An ImageMagick "exec" component for PHP apps.

Installation
===============

Install with [Composer](https://getcomposer.org/), it's the best packages manager you can have :

```shell
composer require orbitale/imagemagick-php
```

Requirements
===============

* PHP 7.2 or higher
* [ImageMagick 7](https://www.imagemagick.org/) has to be installed on your server, and the binaries must be executable by the user running the PHP process.

Settings
===============

There are not many settings, but when you instantiate a new `Command` object, you may specify ImageMagick's executable directory directly in the constructor, for example :

```php
use Orbitale\Component\ImageMagick\Command;

// Default directory for many Linux distributions
$command = new Command('/usr/bin/magick');

// Or in Windows, depending of the install directory
$command = new Command('C:\ImageMagick\magick.exe');

// Will try to automatically discover the path of ImageMagick in your system
// Note: it uses Symfony's ExecutableFinder to find it in $PATH
$command = new Command();
```

The constructor will automatically search for the `magick` executable, test it, and throw an exception if it's not available.

⚠️ Make sure your ImageMagick binary is executable.

Usage
===============

First, we recommend you to note all possible scripts that you can use with ImageMagick in the [official docs](https://imagemagick.org/script/command-line-tools.php):

* [animate](https://imagemagick.org/script/animate.php)
* [compare](https://imagemagick.org/script/compare.php)
* [composite](https://imagemagick.org/script/composite.php)
* [conjure](https://imagemagick.org/script/conjure.php)
* [convert](https://imagemagick.org/script/convert.php)
* [display](https://imagemagick.org/script/display.php)
* [identify](https://imagemagick.org/script/identify.php)
* [import](https://imagemagick.org/script/import.php)
* [mogrify](https://imagemagick.org/script/mogrify.php)
* [montage](https://imagemagick.org/script/montage.php)
* [stream](https://imagemagick.org/script/stream.php)

These correspond to the "legacy binaries", and you can use them if you are familiar or comfortable with them.

As of ImageMagick 7, these are not mandatory, but this package is compatible with them.

### Basic image type converter with ImageMagick's basic logo

Read the comments :

```php
require_once 'vendor/autoload.php';

use Orbitale\Component\ImageMagick\Command;

// Create a new command
$command = new Command();

$response = $command
    // The command will search for the "logo.png" file. If it does not exist, it will throw an exception.
    // If it does, it will create a new command with this source image.
    ->convert('logo.png')

    // The "output()" method will append "logo.gif" at the end of the command-line instruction as a filename.
    // This way, we can continue writing our command without appending "logo.gif" ourselves.
    ->output('logo.gif')

    // At this time, the command shall look like this :
    // $ "{ImageMagickPath}convert" "logo.png" "logo.gif"

    // Then we run the command by using "exec()" to get the CommandResponse
    ->run()
;

// Check if the command failed and get the error if needed
if ($response->hasFailed()) {
    throw new Exception('An error occurred:'.$response->getError());
} else {
    // If it has not failed, then we simply send it to the buffer
    header('Content-type: image/gif');
    echo file_get_contents('logo.gif');
}
```

### Resizing an image

```php
require_once 'vendor/autoload.php';

use Orbitale\Component\ImageMagick\Command;

// Create a new command
$command = new Command();

$response = $command

    ->convert('background.jpeg')
    
    // We'll use the same output as the input, therefore will overwrite the source file after resizing it.
    ->output('background.jpeg')

    // The "resize" method allows you to add a "Geometry" operation.
    // It must fit to the "Geometry" parameters in the ImageMagick official documentation (see links below & phpdoc)
    ->resize('50x50')

    ->run()
;

// Check if the command failed and get the error if needed
if ($response->hasFailed()) {
    throw new Exception('An error occurred:'.$response->getError());
} else {
    // If it has not failed, then we simply send it to the buffer
    header('Content-type: image/gif');
    echo file_get_contents('logo.gif');
}
```

### Currently supported options:

There are **a lot** of command-line options, and each have its own validation system.
 
This is why a "few" ones are implemented now, to make sure validation is possible for each of them.

**Note:** If an option is not implemented in the `Command` class, you can create an issue or make a Pull Request that implements the new option!

* [`-annotate`](http://www.imagemagick.org/script/command-line-options.php#annotate)
* [`-background`](http://www.imagemagick.org/script/command-line-options.php#background)
* [`-blur`](http://www.imagemagick.org/script/command-line-options.php#blur)
* [`-colorspace`](http://www.imagemagick.org/script/command-line-options.php#colorspace)
* [`-crop`](http://www.imagemagick.org/script/command-line-options.php#crop)
* [`-depth`](http://www.imagemagick.org/script/command-line-options.php#depth)
* [`-draw`](http://www.imagemagick.org/script/command-line-options.php#draw)
* [`-extent`](http://www.imagemagick.org/script/command-line-options.php#extent)
* [`-fill`](http://www.imagemagick.org/script/command-line-options.php#fill)
* [`-flatten`](http://www.imagemagick.org/script/command-line-options.php#flatten)
* [`-font`](http://www.imagemagick.org/script/command-line-options.php#font)
* [`-gaussian-blur`](http://www.imagemagick.org/script/command-line-options.php#gaussian-blur)
* [`-gravity`](http://www.imagemagick.org/script/command-line-options.php#gravity)
* [`-interlace`](http://www.imagemagick.org/script/command-line-options.php#interlace)
* [`-monochrome`](http://www.imagemagick.org/script/command-line-options.php#monochrome)
* [`-pointsize`](http://www.imagemagick.org/script/command-line-options.php#pointsize)
* [`-quality`](http://www.imagemagick.org/script/command-line-options.php#quality)
* [`-resize`](http://www.imagemagick.org/script/command-line-options.php#resize)
* [`-rotate`](http://www.imagemagick.org/script/command-line-options.php#rotate)
* [`-size`](http://www.imagemagick.org/script/command-line-options.php#size)
* [`-strip`](http://www.imagemagick.org/script/command-line-options.php#strip)
* [`-stroke`](http://www.imagemagick.org/script/command-line-options.php#stroke)
* [`-thumbnail`](http://www.imagemagick.org/script/command-line-options.php#thumbnail)
* [`-transpose`](http://www.imagemagick.org/script/command-line-options.php#transpose)
* [`-transverse`](http://www.imagemagick.org/script/command-line-options.php#transverse)
* [`xc:`](http://www.imagemagick.org/Usage/canvas/)

Feel free to ask/create an issue if you need more!

### Some aliases that do magic for you:

* `$command->text()`:
This method uses multiple options added to the `-annotate` one to generate a text block.
You must specify its position and size, but you can specify color and the font file used.

* `$command->ellipse()`: (check source code for the heavy prototype!)
This method uses the `-stroke`, `-fill` and `-draw` options to create an ellipse/circle/disc on your picture.
**Note:** I recommend to check both the source code and the documentation to be sure of what you are doing.

Useful links
===============

* ImageMagick official website: http://www.imagemagick.org
* ImageMagick documentation:
    * [Installation of the binaries](https://www.imagemagick.org/script/download.php) (depending on your OS and/or distribution)
    * [Geometry option](https://www.imagemagick.org/script/command-line-processing.php#geometry) (to resize or place text)
    * [All command-line options](https://imagemagick.org/script/command-line-options.php) ; they're not all available in this tool for now, so feel free to make a PR ! ;)
