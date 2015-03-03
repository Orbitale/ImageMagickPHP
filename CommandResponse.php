<?php
/*
* This file is part of the PierstovalImageMagickPHP package.
*
* (c) Alexandre "Pierstoval" Rock Ancelet <pierstoval@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Pierstoval\Component\ImageMagick;


class CommandResponse
{

    protected $content;

    protected $code;

    public function __construct(array $content, $code)
    {
        $this->content = $content;
        $this->code = (int)$code;
    }

    public function hasFailed()
    {
        return $this->code !== 0;
    }

    public function getContent()
    {
        return $this->content;
    }

}
