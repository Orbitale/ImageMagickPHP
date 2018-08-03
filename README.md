ImageMagickPHP
===============

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ff8b439c-772a-495e-9780-4e8e8e451254/mini.png)](https://insight.sensiolabs.com/projects/ff8b439c-772a-495e-9780-4e8e8e451254)
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

* PHP 7.1 or higher
* symfony/yaml 3.0 or higher
* symfony/process 3.0 or higher
* smyfony/filesystem 3.0 or higher
* The `exec()` function **must** be available and work in order to communicate with ImageMagick executables.
* [ImageMagick](http://www.imagemagick.org/) has to be installed on your server, and the binaries must be executable by `exec()`.

Settings
===============

There are not many settings, but when you instanciate a new `Command` object, you may specify ImageMagick's executable directory directly in the constructor, for example :

```php
use Orbitale\Component\ImageMagick\Command;

// Default directory for many Linux distributions:
$command = new Command('/usr/bin');

// Or in Windows, depending of the install directory:
$command = new Command('C:\ImageMagick');

// If it is available in the global scope for the user running the script:
$command = new Command();
```

The constructor will automatically search for the `convert` executable, test it, and throw an exception if it's not available.

/!\ Make sure your ImageMagick executables have the "+x" chmod option, and that the user has the rights to execute it.

Usage
===============

### Basic image converter with ImageMagick's basic logo

Read the comments :

```php
require_once 'vendor/autoload.php'; // A classic one, if you know how Composer works.

use Orbitale\Component\ImageMagick\Command;

// Create a new command
$command = new Command();

$response = $command
    // The command will search for the "logo.png" file. If it does not exist, it will throw an exception.
    // If it does, it will create a new command with this source image.
    ->convert('logo.png')

    // The "file()" method will simply add a file name to the command.
    // For this one, it will be used as an output file name,
    //  that's why we set the 2nd argument "checkExistence" to "false",
    //  so the Command will not check if the file exists (because it would throw an exception then)
    ->file('logo.gif', false)

    // At this time, the command shall look like this :
    // $ "{ImageMagickPath}convert" "logo.png" "logo.gif"

    // Then we run the command by using "exec()" to get the CommandResponse
    ->run();

if (!$response->hasFailed()) {
    // If it has not failed, then we simply send it to the buffer
    header('Content-type: image/gif');
    echo file_get_contents('logo.gif');
}
```

### Simply resizing an image

```php
require_once 'vendor/autoload.php';

use Orbitale\Component\ImageMagick\Command;

// Create a new command
$command = new Command();

$response = $command

    // Here we are using "mogrify", so the file must exist as it is overwritten (it's basically the difference between "convert" and "mogrify")
    ->mogrify('background.jpeg')

    // The "resize" method allows you to add a "Geometry" operation.
    // It must fit to the "Geometry" parameters in the ImageMagick official documentation (see links below)
    ->resize('50x50')

    ->run()
;

if (!$response->hasFailed()) {
    header('Content-type: image/jpeg');
    echo file_get_contents('background.jpeg');
}
```

### Supported commands:

* `convert`
* `mogrify`
* `identify`

### Currently supported options:

There are **TONS** of command-line options, and each have its own validation system.
 
This is why a "few" ones are implemented now.

**Note:** If an option is not implemented in the `Command` class, you should create an issue or make a Pull Request that implements the new option!

* [`-background`](http://www.imagemagick.org/script/command-line-options.php#background)
* [`-crop`](http://www.imagemagick.org/script/command-line-options.php#crop)
* [`-extent`](http://www.imagemagick.org/script/command-line-options.php#extent)
* [`-resize`](http://www.imagemagick.org/script/command-line-options.php#resize)
* [`-thumbnail`](http://www.imagemagick.org/script/command-line-options.php#thumbnail)
* [`-quality`](http://www.imagemagick.org/script/command-line-options.php#quality)
* [`-rotate`](http://www.imagemagick.org/script/command-line-options.php#rotate)

Feel free to ask if you want more!

### Some aliases that do magic for you:

* `Command::text()`:
This method uses multiple options added to the `-annotate` one to generate a text block.
You must specify its position and size, but you can specify color and the font file used.
See [`-annotate`](http://www.imagemagick.org/script/command-line-options.php#annotate) documentation.

* `Command::ellipse()`: (check source code for the heavy prototype!)
This method uses the `-stroke`, `-fill` and `-draw` property to create an ellipse/circle/disc on your picture.
**Note:** I recommend to check both the source code and the documentation to be sure of what you are doing.
See [`-draw`](http://www.imagemagick.org/script/command-line-options.php#draw) documentation.

Useful links
===============

* ImageMagick official website: http://www.imagemagick.org
* ImageMagick documentation:
    * [Installation binaries](http://www.imagemagick.org/script/binary-releases.php) (depending on your OS and/or distribution)
    * [Geometry](http://www.imagemagick.org/script/command-line-processing.php#geometry) (to resize or place text)
    * [All command-line options](http://www.imagemagick.org/ImageMagick-7.0.0/script/command-line-options.php) ; they're not all available for now, so feel free to make a PR ! ;)
