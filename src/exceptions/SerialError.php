<?php


namespace datagutten\phpSerial\exceptions;


use Throwable;

class SerialError extends SerialException
{
    /**
     * @var array
     */
    private $error;

    function __construct(array $error, $message='', $code = 0, Throwable $previous = null)
    {
        $this->error = $error;
        if(!empty($message))
            $message = sprintf('%s: %s', $message, $error['message']);
        else
            $message = $error['message'];

        parent::__construct($message, $code, $previous);
    }
}