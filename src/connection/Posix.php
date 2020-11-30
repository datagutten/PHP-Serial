<?php


namespace datagutten\phpSerial\connection;


use datagutten\phpSerial\exceptions;
use InvalidArgumentException;
use Symfony\Component\Process\Process;

abstract class Posix extends Connection
{
    /**
     * @param string|array $arg Argument(s) to stty
     * @param string $message Error message
     * @return Process
     * @throws exceptions\ProcessException
     */
    abstract protected function stty($arg, string $message): Process;

    public function setBaudRate(int $rate)
    {
        return $this->stty($rate, 'Unable to set baud rate');
    }

    public function setParity(string $parity)
    {
        $args = array(
            "none" => "-parenb",
            "odd"  => "parenb parodd",
            "even" => "parenb -parodd",
        );

        if(!isset($parity))
            throw new InvalidArgumentException('Invalid parity');

        return $this->stty($args[$parity], 'Unable to set parity');
    }

    public function setCharacterLength(int $length)
    {
        if($length<5 || $length>8)
            throw new InvalidArgumentException('Invalid character length');

        return $this->stty('cs'.$length, 'Unable to set character length');
    }

    public function setStopBits(float $length)
    {
        if($length==1)
            $arg = '-cstopb';
        elseif ($length==2)
            $arg = 'cstopb';
        else
            throw new InvalidArgumentException('Stop bit length must be 1 or 2');

        return $this->stty($arg, 'Unable to set stop bit length');
    }

    function setFlowControl(string $mode)
    {
        if($mode==='none')
            $args = ['clocal', 'crtscts', '-ixon', '-ixoff'];
        elseif ($mode==='rts/cts')
            $args = ['-clocal', 'crtscts', '-ixon', '-ixoff'];
        elseif ($mode==='xon/xoff')
            $args = ['-clocal', '-crtscts', 'ixon', 'ixoff'];
        else
            throw new InvalidArgumentException('Invalid flow control mode specified');

        return $this->stty($args, 'Unable to set flow control');
    }

}