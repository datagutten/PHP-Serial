<?php


namespace datagutten\phpSerial\connection;


use datagutten\phpSerial\exceptions;
use datagutten\phpSerial\exceptions\SerialException;
use Symfony\Component\Process\Process;

class OSX extends Posix
{
    /**
     * OSX constructor.
     *
     * @param string $device use the device address, like /dev/tty.serial
     * @throws exceptions\InvalidSerialPort
     * @throws exceptions\SerialException
     */
    function __construct(string $device)
    {
        $process = new Process(['stty', '-f', $device]);
        $process->run();
        if(!$process->isSuccessful())
            throw new exceptions\InvalidSerialPort($process->getErrorOutput());
        $this->device = $device;
        $this->open();
    }

    protected function stty($args, string $message): Process
    {
        if(!is_array($args))
            $args = [$args];

        return $this->exec(['stty', '-f', $this->device] + $args, $message);
    }
}