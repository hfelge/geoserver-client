<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hfelge\GeoServerClient\GeoServerClient;
use Hfelge\GeoServerClient\WorkspaceManager;

class GeoServerClientTest extends TestCase
{
    protected GeoServerClient $client;

    protected function setUp(): void
    {
        $this->client = new GeoServerClient(
            'https://example.com/geoserver',
            'admin',
            'geoserver'
        );
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(GeoServerClient::class, $this->client);
    }

    #[Test]
    public function it_initializes_workspace_manager(): void
    {
        $this->assertInstanceOf(
            WorkspaceManager::class,
            $this->client->workspaceManager
        );
    }
}
