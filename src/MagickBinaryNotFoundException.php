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

namespace Orbitale\Component\ImageMagick;

use Exception;

class MagickBinaryNotFoundException extends Exception
{
    public function __construct(string $magickBinaryPath)
    {
        parent::__construct(\sprintf(
            'The specified path ("%s") is not a file.'."\n".
            'You must set the "magickBinaryPath" parameter as the main "magick" binary installed by ImageMagick.',
            $magickBinaryPath
        ));
    }
}
