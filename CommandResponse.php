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


use Symfony\Component\Process\Process;

class CommandResponse
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    private $output;

    /**
     * @var string
     */
    private $error;

    /**
     * CommandResponse constructor.
     * @param Process $process
     * @param int $code
     * @param string $output
     * @param string $error
     */
    public function __construct(Process $process, $code, $output, $error)
    {
        $this->code    = $code;
        $this->output = $output;
        $this->error = $error;
        $this->process = $process;
    }

    /**
     * @return bool
     */
    public function hasFailed()
    {
        return $this->code !== 0;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

}
