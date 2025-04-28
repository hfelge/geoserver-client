<?php

use PHPUnit\Framework\TestCase;
use Hfelge\GeoServerClient\GeoServerClient;

class GeoServerClientTest extends TestCase
{
    public function testClientInitialization()
    {
        $client = new GeoServerClient(
            'https://example.com/geoserver',
            'admin',
            'geoserver'
        );

        $this->assertInstanceOf(GeoServerClient::class, $client);
    }
}
