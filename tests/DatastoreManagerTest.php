<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hfelge\GeoServerClient\GeoServerClient;
use Hfelge\GeoServerClient\DatastoreManager;

class DatastoreManagerTest extends TestCase
{
    protected DatastoreManager $datastoreManager;
    protected GeoServerClient $mockClient;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(GeoServerClient::class);
        $this->datastoreManager = new DatastoreManager($this->mockClient);
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(DatastoreManager::class, $this->datastoreManager);
    }

    #[Test]
    public function it_returns_true_if_datastore_exists(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/workspaces/testworkspace/datastores/teststore.json')
            ->willReturn(['status' => 200, 'body' => '']);

        $result = $this->datastoreManager->datastoreExists('testworkspace', 'teststore');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_if_datastore_does_not_exist(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/workspaces/testworkspace/datastores/teststore.json')
            ->willReturn(['status' => 404, 'body' => '']);

        $result = $this->datastoreManager->datastoreExists('testworkspace', 'teststore');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_creates_postgis_datastore_successfully(): void
    {
        $this->mockClient->method('request')
            ->with(
                'POST',
                '/rest/workspaces/testworkspace/datastores',
                $this->callback(function ($payload) {
                    $data = json_decode($payload, true);
                    return isset($data['dataStore']['connectionParameters']['dbtype'])
                           && $data['dataStore']['connectionParameters']['dbtype'] === 'postgis';
                })
            )
            ->willReturn(['status' => 201, 'body' => '']);

        $result = $this->datastoreManager->createPostGISDatastore('testworkspace', 'teststore', [
            'host' => 'localhost',
            'port' => '5432',
            'database' => 'gisdb',
            'user' => 'geo_user',
            'passwd' => 'secret'
        ]);

        $this->assertTrue($result);
    }
}
