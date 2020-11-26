<?php


namespace datagutten\phpSerial\connection;


use datagutten\phpSerial\exceptions;
use Symfony\Component\Process\Process;

class Linux extends Connection
{
    /**
     * Linux constructor.
     * use the device address, like /dev/ttyS0
     * @param string $device
     * @throws exceptions\InvalidSerialPort
     * @throws exceptions\SerialException
     */
    function __construct(string $device)
    {
        $device = self::convert_port($device);
        $process = new Process(['stty', '-F', $device]);
        $process->run();
        if(!$process->isSuccessful())
            throw new exceptions\InvalidSerialPort($process->getErrorOutput());
        $this->device = $device;
        $this->open();
    }

    public static function convert_port($device)
    {
        if (preg_match("@^COM(\\d+):?$@i", $device, $matches))
            return "/dev/ttyS" . ($matches[1] - 1);
        else
            return $device;
    }

    function setBaudRate(int $rate)
    {
        $process = new Process(['stty', '-F', $this->device, $rate]);
        $process->run();
        if(!$process->isSuccessful())
            throw new exceptions\ProcessException('Unable to set baud rate', $process);
    }
}