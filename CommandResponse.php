<?php
/*
* This file is part of the OrbitaleImageMagickPHP package.
*
* (c) Alexandre Rock Ancelet <alex@orbitale.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Orbitale\Component\ImageMagick;


class CommandResponse
{

    protected $content;

    protected $code;

    public function __construct(array $content, $code)
    {
        $this->content = $content;
        $this->code    = (int) $code;
    }

    public function hasFailed()
    {
        return $this->code !== 0;
    }

    public function getContent($flatten = false)
    {
        return $flatten ? implode("\n", $this->content) : $this->content;
    }

}
