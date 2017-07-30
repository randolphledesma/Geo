<?php

use PHPUnit\Framework\TestCase;

/**
 * @covers Utils
 */
final class GeoTest extends TestCase
{
    protected $geo;

    public function setUp()
    {
        $this->geo = new Geo();
        $this->geo->configure([
            'apikey'=>''
        ]);
    }

    public function testMiles1()
    {        
        $from = 'From this address';
        $to = 'To this address';
        $this->assertEquals($this->geo->miles($from, $to), 17.81, '');
    }
}
