<?php

declare(strict_types=1);

use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;
use Hfelge\GeoServerClient\GeoServerException;
use PHPUnit\Framework\Attributes\Test;

class LayerManagerTest extends TestCaseWithGeoServerClient
{
    protected string $workspace = 'phpunit_ws';
    protected string $datastore = 'phpunit_ds';
    protected string $featureType = 'phpunit_layer';

    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->client->workspaceManager->workspaceExists($this->workspace)) {
            $this->client->workspaceManager->createWorkspace($this->workspace);
        }

        if (!$this->client->datastoreManager->datastoreExists($this->workspace, $this->datastore)) {
            $this->client->datastoreManager->createPostGISDatastore($this->workspace, $this->datastore, [
                'host'     => getenv('GEOSERVER_DB_HOST')     ?: 'db',
                'port'     => getenv('GEOSERVER_DB_PORT')     ?: '5432',
                'database' => getenv('GEOSERVER_DB_NAME')     ?: 'db',
                'user'     => getenv('GEOSERVER_DB_USER')     ?: 'db',
                'passwd'   => getenv('GEOSERVER_DB_PASSWORD') ?: 'db',
            ]);
        }

        if (!$this->client->featureTypeManager->featureTypeExists($this->workspace, $this->datastore, $this->featureType)) {
            $this->client->featureTypeManager->createFeatureType($this->workspace, $this->datastore, [
                'name'       => $this->featureType,
                'nativeName' => $this->featureType,
                'title'      => 'PHPUnit Layer',
                'srs'        => 'EPSG:4326',
            ]);
        }
    }

    protected function tearDown(): void
    {
        $this->client->layerManager->deleteLayer($this->featureType);
        $this->client->featureTypeManager->deleteFeatureType($this->workspace, $this->datastore, $this->featureType);
        $this->client->datastoreManager->deleteDatastore($this->workspace, $this->datastore);
        $this->client->workspaceManager->deleteWorkspace($this->workspace, true);
    }

    #[Test]
    public function it_can_list_layers(): void
    {
        $result = $this->client->layerManager->getLayers();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('layers', $result);
    }

    #[Test]
    public function it_can_get_a_layer(): void
    {
        $this->client->layerManager->publishLayer($this->featureType);
        $layer = $this->client->layerManager->getLayer($this->featureType);
        $this->assertEquals($this->featureType, $layer['layer']['name']);
    }

    #[Test]
    public function it_returns_false_for_nonexistent_layer(): void
    {
        $layer = $this->client->layerManager->getLayer('does_not_exist');
        $this->assertFalse($layer);
    }

    #[Test]
    public function it_can_check_if_layer_exists(): void
    {
        $this->client->layerManager->publishLayer($this->featureType);
        $this->assertTrue($this->client->layerManager->layerExists($this->featureType));
    }

    #[Test]
    public function it_returns_false_when_layer_does_not_exist(): void
    {
        $this->assertFalse($this->client->layerManager->layerExists('not_real'));
    }

    #[Test]
    public function it_can_publish_layer(): void
    {
        $success = $this->client->layerManager->publishLayer($this->featureType);
        $this->assertTrue($success);
    }

    #[Test]
    public function it_returns_false_when_publishing_invalid_layer(): void
    {
        $this->assertFalse($this->client->layerManager->publishLayer('invalid@@@'));
    }

    #[Test]
    public function it_can_update_layer(): void
    {
        $this->client->layerManager->publishLayer($this->featureType);
        $updated = $this->client->layerManager->updateLayer($this->featureType, [
            'enabled' => true,
        ]);
        $this->assertTrue($updated);
    }

    #[Test]
    public function it_returns_false_when_updating_nonexistent_layer(): void
    {
        $result = $this->client->layerManager->updateLayer('not-there', ['enabled' => false]);
        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_delete_layer(): void
    {
        $this->client->layerManager->publishLayer($this->featureType);
        $deleted = $this->client->layerManager->deleteLayer($this->featureType);
        $this->assertTrue($deleted);
        $this->assertFalse($this->client->layerManager->layerExists($this->featureType));
    }

    #[Test]
    public function it_returns_false_when_deleting_nonexistent_layer(): void
    {
        $this->assertFalse($this->client->layerManager->deleteLayer('not_existing'));
    }

    #[Test]
    public function it_returns_false_for_invalid_layer_name(): void
    {
        $this->assertFalse($this->client->layerManager->getLayer('@@@'));
    }
}
