<?php


use datagutten\phpSerial;
use PHPUnit\Framework\TestCase;

class SerialConnectionTest extends TestCase
{

    public function testInvalidPort()
    {
        $this->expectException(phpSerial\exceptions\InvalidSerialPort::class);
        phpSerial\Serial::open('COMbad');
    }
}
