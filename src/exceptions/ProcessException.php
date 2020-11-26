<?php


namespace datagutten\phpSerial\exceptions;


use Symfony\Component\Process\Process;
use Throwable;

class ProcessException extends SerialException
{
    public $process;
    function __construct($message, Process $process, $code = 0, Throwable $previous = null)
    {
        $this->process = $process;
        $message = sprintf('%s: %s', $message, $process->getErrorOutput());
        parent::__construct($message, $code, $previous);
    }
}