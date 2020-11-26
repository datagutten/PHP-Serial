<?php


namespace datagutten\phpSerial;


use datagutten\phpSerial\connection;
use datagutten\phpSerial\exceptions\SerialException;

class Serial
{
    /**
     * @param $device
     * @return connection\Connection
     * @throws SerialException
     */
    public static function open($device)
    {
        switch (PHP_OS)
        {
            case 'WINNT': return new connection\Windows($device);
            case 'Linux': return new connection\Linux($device);
            default: throw new SerialException(sprintf('Unsupported operating system: %s', PHP_OS));
        }
    }
}