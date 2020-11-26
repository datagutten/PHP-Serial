<?php


namespace datagutten\phpSerial\connection;


use datagutten\phpSerial\exceptions;
use InvalidArgumentException;
use Symfony\Component\Process\Process;

class Windows extends Connection
{
    /**
     * Windows constructor.
     * @param string $device use the COMxx device name, like COM1
     * @inheritDoc
     */
    function __construct(string $device)
    {
        $process = new Process(['mode', $device, 'xon=on', 'BAUD=9600']);
        $process->run();
        if(!$process->isSuccessful())
            throw new exceptions\InvalidSerialPort($process->getErrorOutput());
        $this->device = $device;
        $this->open();
    }

    public function setBaudRate(int $rate)
    {
        $validBauds = array (
            110    => 11,
            150    => 15,
            300    => 30,
            600    => 60,
            1200   => 12,
            2400   => 24,
            4800   => 48,
            9600   => 96,
            19200  => 19,
            38400  => 38400,
            57600  => 57600,
            115200 => 115200
        );

        if(!isset($validBauds[$rate]))
            throw new InvalidArgumentException(sprintf('Invalid baud rate %d', $rate));

        $process = new Process(['mode', $this->device, 'BAUD='.$validBauds[$rate]]);
        if(!$process->isSuccessful())
            throw new exceptions\ProcessException('Unable to set baud rate', $process);
    }
}