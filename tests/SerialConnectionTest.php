<?php


use datagutten\phpSerial;
use datagutten\phpSerial\connection\Connection;
use PHPUnit\Framework\TestCase;

class SerialConnectionTest extends TestCase
{

    public function testInvalidPort()
    {
        $this->expectException(phpSerial\exceptions\InvalidSerialPort::class);
        phpSerial\Serial::open('COMbad');
    }

    public function testConvertedPort()
    {
        $connection = phpSerial\Serial::open('COM1');
        $this->assertInstanceOf(Connection::class, $connection);
    }
}
