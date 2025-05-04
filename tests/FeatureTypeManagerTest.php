<?php

declare(strict_types=1);

use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;
use Hfelge\GeoServerClient\GeoServerException;
use PHPUnit\Framework\Attributes\Test;

class FeatureTypeManagerTest extends TestCaseWithGeoServerClient
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
                'host'     => getenv( 'GEOSERVER_DB_HOST' ) ?: 'db',
                'port'     => getenv( 'GEOSERVER_DB_PORT' ) ?: '5432',
                'database' => getenv( 'GEOSERVER_DB_NAME' ) ?: 'db',
                'user'     => getenv( 'GEOSERVER_DB_USER' ) ?: 'db',
                'passwd'   => getenv( 'GEOSERVER_DB_PASSWORD' ) ?: 'db',
            ]);
        }
    }

    protected function tearDown(): void
    {
        $this->client->featureTypeManager->deleteFeatureType($this->workspace, $this->datastore, $this->featureType);
        $this->client->datastoreManager->deleteDatastore($this->workspace, $this->datastore);
        $this->client->workspaceManager->deleteWorkspace($this->workspace, true);
    }

    #[Test]
    public function it_can_list_feature_types(): void
    {
        $result = $this->client->featureTypeManager->getFeatureTypes($this->workspace, $this->datastore);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('featureTypes', $result);
    }

    #[Test]
    public function it_can_create_a_feature_type(): void
    {
        $created = $this->client->featureTypeManager->createFeatureType($this->workspace, $this->datastore, [
            'name' => $this->featureType,
            'nativeName' => $this->featureType,
            'title' => 'PHPUnit Layer',
            'srs' => 'EPSG:4326'
        ]);

        $this->assertTrue($created);
        $this->assertTrue($this->client->featureTypeManager->featureTypeExists($this->workspace, $this->datastore, $this->featureType));
    }

    #[Test]
    public function it_returns_false_when_creating_existing_feature_type(): void
    {
        $this->client->featureTypeManager->createFeatureType($this->workspace, $this->datastore, [
            'name' => $this->featureType,
            'nativeName' => $this->featureType,
            'title' => 'PHPUnit Layer',
            'srs' => 'EPSG:4326'
        ]);

        $secondTry = $this->client->featureTypeManager->createFeatureType($this->workspace, $this->datastore, [
            'name' => $this->featureType,
            'nativeName' => $this->featureType,
            'title' => 'PHPUnit Layer',
            'srs' => 'EPSG:4326'
        ]);

        $this->assertFalse($secondTry);
    }

    #[Test]
    public function it_can_get_a_feature_type(): void
    {
        $this->client->featureTypeManager->createFeatureType($this->workspace, $this->datastore, [
            'name' => $this->featureType,
            'nativeName' => $this->featureType,
            'title' => 'PHPUnit Layer',
            'srs' => 'EPSG:4326'
        ]);

        $result = $this->client->featureTypeManager->getFeatureType($this->workspace, $this->datastore, $this->featureType);
        $this->assertEquals($this->featureType, $result['featureType']['name']);
    }

    #[Test]
    public function it_returns_false_for_non_existing_feature_type(): void
    {
        $result = $this->client->featureTypeManager->getFeatureType($this->workspace, $this->datastore, 'doesnotexist');
        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_update_a_feature_type(): void
    {
        $this->client->featureTypeManager->createFeatureType($this->workspace, $this->datastore, [
            'name' => $this->featureType,
            'nativeName' => $this->featureType,
            'title' => 'PHPUnit Layer',
            'srs' => 'EPSG:4326'
        ]);

        $updated = $this->client->featureTypeManager->updateFeatureType($this->workspace, $this->datastore, $this->featureType, [
            'title' => 'Updated Layer Title'
        ]);

        $this->assertTrue($updated);
    }

    #[Test]
    public function it_returns_false_when_updating_nonexistent_feature_type(): void
    {
        $updated = $this->client->featureTypeManager->updateFeatureType($this->workspace, $this->datastore, 'missing', [
            'title' => 'Nothing'
        ]);

        $this->assertFalse($updated);
    }

    #[Test]
    public function it_can_delete_a_feature_type(): void
    {
        $this->client->featureTypeManager->createFeatureType($this->workspace, $this->datastore, [
            'name' => $this->featureType,
            'nativeName' => $this->featureType,
            'title' => 'PHPUnit Layer',
            'srs' => 'EPSG:4326'
        ]);

        $deleted = $this->client->featureTypeManager->deleteFeatureType($this->workspace, $this->datastore, $this->featureType);
        $this->assertTrue($deleted);
        $this->assertFalse($this->client->featureTypeManager->featureTypeExists($this->workspace, $this->datastore, $this->featureType));
    }

    #[Test]
    public function it_returns_false_when_deleting_nonexistent_feature_type(): void
    {
        $deleted = $this->client->featureTypeManager->deleteFeatureType($this->workspace, $this->datastore, 'not-there');
        $this->assertFalse($deleted);
    }

    #[Test]
    public function it_throws_exception_for_invalid_workspace(): void
    {
        $this->expectException(GeoServerException::class);
        $this->client->featureTypeManager->getFeatureTypes('@@@', 'whatever');
    }
}
