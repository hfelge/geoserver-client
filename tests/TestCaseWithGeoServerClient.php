<?php

declare(strict_types=1);

namespace Hfelge\GeoServerClient\Tests;

use PHPUnit\Framework\TestCase;
use Hfelge\GeoServerClient\GeoServerClient;

abstract class TestCaseWithGeoServerClient extends TestCase
{
    protected ?GeoServerClient $client = null;

    protected function setUp(): void
    {
        parent::setUp();

        $baseUrl  = getenv('GEOSERVER_URL')      ?: 'http://host.docker.internal:8080/geoserver';
        $username = getenv('GEOSERVER_USERNAME') ?: 'admin';
        $password = getenv('GEOSERVER_PASSWORD') ?: 'geoserver';

        $this->client = new GeoServerClient($baseUrl, $username, $password);

        if (!$this->client->isAvailable()) {
            $this->markTestSkipped("GeoServer not available at {$baseUrl}.");
        }
    }
}
