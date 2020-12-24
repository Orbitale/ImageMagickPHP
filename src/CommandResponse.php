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

    public function __construct(Process $process, int $code, string $output, string $error)
    {
        $this->code = $code;
        $this->output = $output;
        $this->error = $error;
        $this->process = $process;
    }

    public function isSuccessful(): bool
    {
        return 0 === $this->code;
    }

    public function hasFailed(): bool
    {
        return 0 !== $this->code;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
