<?php


namespace datagutten\phpSerial\connection;


use datagutten\phpSerial\exceptions;
use datagutten\phpSerial\exceptions\SerialException;
use Symfony\Component\Process\Process;

abstract class Connection
{
    /**
     * @var string Serial device name
     */
    public $device;

    /**
     * This var says if buffer should be flushed by send (true) or
     * manually (false)
     *
     * @var bool
     */
    public $autoFlush = true;
    /**
     * @var false|resource
     */
    protected $handle;
    protected $buffer;

    /**
     * Connection constructor.
     * @param $device
     * @throws exceptions\InvalidSerialPort
     * @throws exceptions\SerialException
     */
    abstract function __construct(string $device);

    /**
     * @param string $mode
     * @throws exceptions\SerialException
     */
    public function open($mode='r+b')
    {
        $this->handle = @fopen($this->device, $mode);
        if($this->handle===false)
            throw new exceptions\SerialException('Unable to open port: '.error_get_last());
        stream_set_blocking($this->handle, 0);
    }

    /**
     * @throws exceptions\SerialException
     */
    public function close()
    {
        $status = fclose($this->handle);
        if($status===false)
            throw new exceptions\SerialException('Unable to close port: '.error_get_last());
    }

    /**
     * Sends a string to the device
     *
     * @param string $message string to be sent to the device
     * @param float|int $waitForReply time to wait for the reply (in seconds)
     * @throws SerialException
     */
    public function send(string $message, $waitForReply = 1)
    {
        $this->buffer .= $message;

        if ($this->autoFlush === true) {
            $this->flush();
        }

        usleep((int) ($waitForReply * 1000000));
    }

    /**
     * Reads the port until no new datas are availible, then return the content.
     *
     * @param int $count Number of characters to be read (will stop before
     *                   if less characters are in the buffer)
     * @return string
     * @throws exceptions\SerialException
     */
    public function read($count = 0)
    {
        $content = fread($this->handle, 128);

        while (!empty($content_temp) || !isset($content_temp))
        {
            $content_temp = fread($this->handle, 128);
            if($content_temp===false)
                throw new exceptions\SerialError(error_get_last(), 'Error reading from serial port');
            $content .= $content_temp;
            if($count>0 && strlen($content)>=$count)
                break;
        }

        return $content;
    }

    /**
     * Flushes the output buffer
     *
     * @throws exceptions\SerialError
     */
    public function flush()
    {
        $status = fwrite($this->handle, $this->buffer);
        if($status===false)
            throw new exceptions\SerialError(error_get_last(), 'Error flushing');

        $this->buffer = "";
    }

    /**
     * @param $args
     * @param $message
     * @return Process
     * @throws exceptions\ProcessException
     */
    protected function exec(array $args, string $message)
    {
        $process = new Process($args);
        $process->run();
        if(!$process->isSuccessful())
            throw new exceptions\ProcessException($message, $process);
        return $process;
    }

    /**
     * @throws exceptions\SerialException
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Set baud rate
     * @param int $rate Baud rate
     * @throws exceptions\ProcessException
     */
    abstract function setBaudRate(int $rate);

    /**
     * Configure parity.
     * Modes : odd, even, none
     *
     * @param string $parity one of the modes
     * @return bool
     * @throws SerialException
     */
    abstract function setParity(string $parity);

    /**
     * Sets the length of a character.
     *
     * @param int $length length of a character (5 <= length <= 8)
     * @return bool
     * @throws SerialException
     */
    abstract function setCharacterLength(int $length);

    /**
     * Sets the length of stop bits.
     *
     * @param float $length the length of a stop bit. It must be either 1,
     *                       1.5 or 2. 1.5 is not supported under linux and on
     *                       some computers.
     * @return bool
     * @throws SerialException
     */
    abstract function setStopBits(float $length);

    /**
     * Configures the flow control
     *
     * @param string $mode Set the flow control mode. Availible modes :
     *                      -> "none" : no flow control
     *                      -> "rts/cts" : use RTS/CTS handshaking
     *                      -> "xon/xoff" : use XON/XOFF protocol
     * @return bool
     * @throws SerialException
     */
    abstract function setFlowControl(string $mode);


}