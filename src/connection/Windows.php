<?php


namespace datagutten\phpSerial\connection;


use datagutten\phpSerial\exceptions;
use datagutten\phpSerial\exceptions\SerialException;
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

    /**
     * @param $args
     * @param $message
     * @return Process
     * @throws exceptions\ProcessException
     */
    protected function mode($args, string $message)
    {
        if(is_array($args))
            return $this->exec(['mode', $this->device] + $args, $message);
        else
            return $this->exec(['mode', $this->device, $args], $message);
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

    function setParity(string $parity)
    {
        return $this->exec(['mode', $this->device, 'PARITY=' . $parity{0}], 'Unable to set parity');
    }

    function setCharacterLength(int $length)
    {
        $length = (int) $length;
        if ($length < 5) {
            $length = 5;
        } elseif ($length > 8) {
            $length = 8;
        }


        return $this->mode('DATA='.$length, 'Unable to set character length');
    }

    function setStopBits(float $length)
    {
        if ($length != 1
            and $length != 2
            and $length != 1.5
        ) {
            throw new InvalidArgumentException(
                "Specified stop bit length is invalid"
            );
        }
        return $this->mode('STOP='.$length, 'Unable to set stop bit length');
    }

    function setFlowControl(string $mode)
    {
        if($mode==='none')
            $args = ['xon=off', 'octs=off', 'rts=on'];
        elseif ($mode==='rts/cts')
            $args = ['xon=off', 'octs=on', 'rts=hs'];
        elseif ($mode==='xon/xoff')
            $args = ['xon=on', 'octs=off', 'rts=on'];
        else
            throw new InvalidArgumentException('Invalid flow control mode specified');

        return $this->mode($args, 'Unable to set flow control');
    }
}