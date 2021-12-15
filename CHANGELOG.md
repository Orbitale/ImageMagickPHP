# [v3.3.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.3.0) - 15 Dec 2021

* Drop support for Symfony 4 (Please upgrade 😉)
* Change minimum required Symfony version to 5.3 instead of 4.0|5.0
* Test the package with more Symfony & PHP versions

# [v3.2.1](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.2.1) - 02 Jul 2021

* Add tests for the `-threshold` option.
* Removed useless method `__toString()` in the Geometry class.
* Remove a check to the regex in the Geometry class that was always returning false.

# [v3.2.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.2.0) - 08 Apr 2021

* Implement the `-page` option ([@pbories](https://github.com/pbories))
* Refactor the CI setup to use ImageMagick Docker image
* Don't make `Command` be so strict about ImageMagick binaries when resolving the `convert` binary.<br>
  This is important because it means that now you can use a compound binary, such as using `docker run ...` or `docker exec ...`.

# [v3.1.1](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.1.1) - 01 Feb 2021

## Fixes

* Fix an issue with `References::rotation()` not taking decimal nor single values in account.
* Fix issues when ImageMagick path is empty when creating a new `Command`.

# [v3.1.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.1.0) - 13 Jan 2021

## New features

Allow multiple sources as first parameter of `convert()` method (#32 by @pbories).

This means you can use the method like this: `->convert(['file1.jpg', 'file2.jpg'])`, for convenience, when converting multiple source files.

## Fixes

* Automatically find `magick` binary (#31 by @pbories).
* Fix issue making path unresolvable if null.
* Fix blur type issues, and add test for it. (#23 by @AMK9978, [83914560](https://github.com/Orbitale/ImageMagickPHP/commit/83914560495b0e911ea3d040d663e32c633868a2) by @pierstoval)
  
## Miscellaneous

* Move source code to "src/" directory instead of project root, easier for conciseness and code coverage.
* Refactor test setup with latest PHPUnit version.
* Global CS fix.

# [v3.0.14](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.14) - 13 Dec 2020

Added PHP 8 support (#28 by @VincentLanglet)

# [v3.0.13](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.13) - 31 Jul 2020

Add support for the `-threshold` option (@fariasmaiquita)

# [v3.0.12](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.12) - 01 Jan 2020

Added support for `-transpose`, `-transverse` and `-monochrome` commands.

# [v3.0.11](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.11) - 27 Dec 2019

Allow Symfony 5.0

# [v3.0.10](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.10) - 12 Dec 2019

Added support for `-gravity` option (@JeffAlyanak)

# [v3.0.9](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.9) - 26 Aug 2019

*  Added `-depth` `-flatten` and `-colorspace` options (@70mmy)

# [v3.0.8](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.8) - 23 May 2019

### Changelog for v3.0.8

* Added support for ImageMagick's `-auto-orient` option 

# [v3.0.6](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.6) - 08 Mar 2019

Changelog for v3.0.6:

* Fix bug in `Command::text()`

# [v3.0.4](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.4) - 07 Mar 2019

Changelog for v3.0.4:

* Add support for  `-geometry`
* Implement `Command::rawCommand()` for edge cases that are not implemented yet. ÔÜá´©Å Use at your own risk! It is way better to create an issue or a pull-request if you need an unimplemented feature ­ƒÿë 

# [v3.0.3](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.3) - 07 Mar 2019

Changelog for v3.0.1:

* Force adding `"` for colors when possible, to allow colors like `rgba()` or other values with parentheses

# [v3.0.2](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.2) - 07 Mar 2019

Changelog for v3.0.2:

* Added support for `-strokewidth` option

# [v3.0.1](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.1) - 07 Mar 2019

Changelog for v3.0.1:

* Added support for `-draw "polyline ..."` raw without validation (more will come about drawing with validation)

# [v3.0.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v3.0.0) - 05 Mar 2019

Changelog for v3.0.0:

* **Drop support for ImageMagick 6, this means that we support only the `magick` binary (or `magick.exe` on Windows)**
* Add preliminary support for all other legacy commands (composite, animation, etc.)
* Require PHP 7.2 and `symfony/process` 4.* only
* Removed `Command::escape()` method
* Use php-cs-fixer to uniformize code style
* Use `declare(strict_types=1);` everywhere
* Use `ExecutableFinder` by default to determine ImageMagick's path, making it easier to discover default `magick` binary
* Command line instructions are now arrays instead of strings (easier handling)

# [v2.0.2](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v2.0.2) - 12 Feb 2019

# Changelog for v2.0.2

## Internal breaks

* Remove internal class `CommandOptions` and put all options in the main class.
* Remove `symfony/yaml` dependency and make references an array for better performances.
* Remove `symfony/filesystem` dependency and use native PHP functions.
* ÔÜá´©Å BC BREAK: Refactor `Command::text()` with array options instead of arguments:
  * `$options['text']`
  * `$options['textSize']`
  * `$options['geometry']`
  * `$options['font']`
  * `$options['checkFont']`
  * `$options['textColor']`

## Features

* Add `Command::create($binary)` for simpler fluent interfaces.
* Add support for more ImageMagick options:
  * `-stroke`
  * `-fill`
  * `-size`
  * `-pointsize`
* Add `$checkFontFileExists` argument to `Command::font()` for native fonts
* Add `isSuccessful()` to `CommandResponse`, more convenient than `hasFailed()`

## Dev/test

* Update Travis-CI build to find a proper way to download ImageMagick for tests (even though it's not 100% ok)

# [v2.0.1](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v2.0.1) - 09 Feb 2018

Changelog for v2.0.1

* Add [`-size`](http://www.imagemagick.org/script/command-line-options.php#size) option
* Add `output()` alias to append file name to the end of the command
* Add `xc` option to allow creating images with "X window color", like `xc:none` by default for transparent images

# [v2.0.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v2.0.0) - 20 Jan 2018

New of the v2.0!
==========

* Now PHP 7.1 is required.
* Symfony 3.0+ or 4.0+ required.
* Make use of PHP 7 typehints when it's possible.
* Tests on Travis-CI now build ImageMagick 6 and 7 to check both versions.

Just a major release to advance in time.

If new ImageMagick options are added, they may be backported to v1, as of the new 1.x branch.

# [v1.6.1](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.6.1) - 19 Jan 2018

### Fixes

* Escape background option

# [v1.6.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.6.0) - 10 Feb 2017

### New features

Add support for `-interlace`, `-blur` and `-gaussian-blur` options.

Added needed references and tests

# [v1.5.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.5.0) - 02 Feb 2017

### New features
- Use Symfony Process to exec command, and take care of IM6 and IM7 **(Possible BC breaks, but there should be none actually)**
- Add `__toString()` to Geometry

### Adjustments
- Always use the bundled reference file instead of passing it as argument
- Improve constructor docs for Geometry

### Fixes
- Add spaces after some command options
- Trim Geometry value when constructing it

Adapted tests to last updates, and remove useless ones

# [v1.4.2](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.4.2) - 01 Feb 2017

### New option

[4e99158] Added `-strip` option

Docs of this option => http://www.imagemagick.org/script/command-line-options.php#strip

# [v1.4.1](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.4.1) - 15 Apr 2016

# New option!

[a897116] Add the `-rotate` command-line option (closes #2 )

# [v1.4.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.4.0) - 08 Mar 2016

Add SF3 compatibility (still need review)

# [v1.3.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.3.0) - 03 Sep 2015

This new release comes with a great `Geometry` validation inside the new `Geometry` class.

Geometry is really hard to validate in ImageMagick because it can have multiple forms, according to [ImageMagick's documentation about Geometry](http://www.imagemagick.org/script/command-line-processing.php#geometry).

Then, all current methods requiring a geometry parameter (`resize`, `crop`, etc.) now allow using a `Geometry` instance to reinforce validation before executing the command (and catching the exceptions with a verbose explanation of what happened).
Also, the `Geometry` class has an easy constructor with `Geometry::createFromParameters` that allow specifying all parameters we need: width, height, X and Y offsets, and the aspect ratio option.

More than 700 tests were generated to be sure that the validation system is the same than the one present in ImageMagick itself.

Also, a big new feature about `::run()` method was introduced: you can now execute commands in three ways:
- Normally (nothing to do)
- In background (will append `> /dev/null 2>&1` to the command) 
- In debug mode (will append STDERR to STDOUT to retrieve the errors in the `CommandResponse::getContent()` method)

Just take a look at the `RUN_*` constants in the `Command` class.

### New features

[ef52968] Start refactoring the Geometry validation
[044f25d] Many updates on the command class. 
- Escape the geometry parameters (to avoid problems with "!" or "^" flags)
- Added "setEnv" for env vars, also added other IM commands: background, crop, extent. 
- When using `run`, one can retrieve stderr in the output to watch for errors. 
- Removed many spaces form most commands.
  [4b46ff0] Add new Geometry class to handle geometry use
  [f1636aa] Created new `RUN_` constants for running mode.
  ### Tests & CI
  [a46c25d] Install ImageMagick manually to have latest version
  [1f440a4] Show ImageMagick version in tests
  [aad6c2b] Finalize geometry testing
  [08576eb] Remove useless dev deps, optimize travis-ci
  [510c3f5] Fixed tests

### Code style

[738047a] Reorder methods to fit PSR style

# [v1.2.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.2.0) - 07 Apr 2015

Transferred ownership.

# [v1.1.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.1.0) - 03 Mar 2015

**New features:**
dd2991b Added "quality" and "thumbnail" command line options
afac867 Made the "escape" function public.

**Other big changes:**
e9cad7d Changed composer namespace

Also added many MANY tests.

# [v1.0.0](https://github.com/Orbitale/ImageMagickPHP/releases/tag/v1.0.0) - 26 Feb 2015

First release
