<?php


namespace datagutten\phpSerial\connection;


use datagutten\phpSerial\exceptions;
use datagutten\phpSerial\exceptions\SerialException;
use Symfony\Component\Process\Process;

class Linux extends Posix
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

    protected function stty($args, string $message): Process
    {
        if(!is_array($args))
            return $this->exec(['stty', '-F', $this->device, $args], $message);
        else
            return $this->exec(['stty', '-F', $this->device] + $args, $message);
    }

    public static function convert_port($device)
    {
        if (preg_match("@^COM(\\d+):?$@i", $device, $matches))
            return "/dev/ttyS" . ($matches[1] - 1);
        else
            return $device;
    }
}