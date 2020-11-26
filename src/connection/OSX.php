<?php


namespace datagutten\phpSerial\connection;


use datagutten\phpSerial\exceptions;
use Symfony\Component\Process\Process;

class OSX extends Connection
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

    function setBaudRate(int $rate)
    {
        $process = new Process(['stty', '-f', $this->device, $rate]);
        $process->run();
        if(!$process->isSuccessful())
            throw new exceptions\ProcessException('Unable to set baud rate', $process);
    }
}