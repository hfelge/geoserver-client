<?php

declare(strict_types=1);

use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;
use Hfelge\GeoServerClient\GeoServerException;
use PHPUnit\Framework\Attributes\Test;

class DatastoreManagerTest extends TestCaseWithGeoServerClient
{
    protected string $workspace = 'phpunit_ws';
    protected string $datastore = 'phpunit_ds';

    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->client->workspaceManager->workspaceExists($this->workspace)) {
            $this->client->workspaceManager->createWorkspace($this->workspace);
        }
    }

    protected function tearDown(): void
    {
        $this->client->datastoreManager->deleteDatastore($this->workspace, $this->datastore);
        $this->client->workspaceManager->deleteWorkspace($this->workspace, true);
    }

    #[Test]
    public function it_can_list_datastores(): void
    {
        $result = $this->client->datastoreManager->getDatastores($this->workspace);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('dataStores', $result);
    }

    #[Test]
    public function it_can_create_a_postgis_datastore(): void
    {
        $created = $this->client->datastoreManager->createPostGISDatastore($this->workspace, $this->datastore, [
            'host'     => getenv( 'GEOSERVER_DB_HOST' ) ?: 'db',
            'port'     => getenv( 'GEOSERVER_DB_PORT' ) ?: '5432',
            'database' => getenv( 'GEOSERVER_DB_NAME' ) ?: 'db',
            'user'     => getenv( 'GEOSERVER_DB_USER' ) ?: 'db',
            'passwd'   => getenv( 'GEOSERVER_DB_PASSWORD' ) ?: 'db',
        ]);

        $this->assertTrue($created);
        $this->assertTrue($this->client->datastoreManager->datastoreExists($this->workspace, $this->datastore));
    }

    #[Test]
    public function it_returns_false_when_creating_existing_datastore(): void
    {
        $this->client->datastoreManager->createPostGISDatastore($this->workspace, $this->datastore, [
            'host'     => getenv( 'GEOSERVER_DB_HOST' ) ?: 'db',
            'port'     => getenv( 'GEOSERVER_DB_PORT' ) ?: '5432',
            'database' => getenv( 'GEOSERVER_DB_NAME' ) ?: 'db',
            'user'     => getenv( 'GEOSERVER_DB_USER' ) ?: 'db',
            'passwd'   => getenv( 'GEOSERVER_DB_PASSWORD' ) ?: 'db',
        ]);

        $secondTry = $this->client->datastoreManager->createPostGISDatastore($this->workspace, $this->datastore, [
            'host'     => getenv( 'GEOSERVER_DB_HOST' ) ?: 'db',
            'port'     => getenv( 'GEOSERVER_DB_PORT' ) ?: '5432',
            'database' => getenv( 'GEOSERVER_DB_NAME' ) ?: 'db',
            'user'     => getenv( 'GEOSERVER_DB_USER' ) ?: 'db',
            'passwd'   => getenv( 'GEOSERVER_DB_PASSWORD' ) ?: 'db',
        ]);

        $this->assertFalse($secondTry);
    }

    #[Test]
    public function it_can_get_a_datastore(): void
    {
        $this->client->datastoreManager->createPostGISDatastore($this->workspace, $this->datastore, [
            'host'     => getenv( 'GEOSERVER_DB_HOST' ) ?: 'db',
            'port'     => getenv( 'GEOSERVER_DB_PORT' ) ?: '5432',
            'database' => getenv( 'GEOSERVER_DB_NAME' ) ?: 'db',
            'user'     => getenv( 'GEOSERVER_DB_USER' ) ?: 'db',
            'passwd'   => getenv( 'GEOSERVER_DB_PASSWORD' ) ?: 'db',
        ]);

        $ds = $this->client->datastoreManager->getDatastore($this->workspace, $this->datastore);
        $this->assertEquals($this->datastore, $ds['dataStore']['name']);
    }

    #[Test]
    public function it_returns_false_for_non_existing_datastore(): void
    {
        $result = $this->client->datastoreManager->getDatastore($this->workspace, 'doesnotexist');
        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_update_a_datastore(): void
    {
        $this->client->datastoreManager->createPostGISDatastore($this->workspace, $this->datastore, [
            'host'     => getenv( 'GEOSERVER_DB_HOST' ) ?: 'db',
            'port'     => getenv( 'GEOSERVER_DB_PORT' ) ?: '5432',
            'database' => getenv( 'GEOSERVER_DB_NAME' ) ?: 'db',
            'user'     => getenv( 'GEOSERVER_DB_USER' ) ?: 'db',
            'passwd'   => getenv( 'GEOSERVER_DB_PASSWORD' ) ?: 'db',
        ]);

        $result = $this->client->datastoreManager->updateDatastore($this->workspace, $this->datastore, [
            'enabled' => true,
        ]);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_updating_nonexistent_datastore(): void
    {
        $result = $this->client->datastoreManager->updateDatastore($this->workspace, 'missing_ds', [
            'enabled' => false,
        ]);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_delete_a_datastore(): void
    {
        $this->client->datastoreManager->createPostGISDatastore($this->workspace, $this->datastore, [
            'host'     => getenv( 'GEOSERVER_DB_HOST' ) ?: 'db',
            'port'     => getenv( 'GEOSERVER_DB_PORT' ) ?: '5432',
            'database' => getenv( 'GEOSERVER_DB_NAME' ) ?: 'db',
            'user'     => getenv( 'GEOSERVER_DB_USER' ) ?: 'db',
            'passwd'   => getenv( 'GEOSERVER_DB_PASSWORD' ) ?: 'db',
        ]);

        $deleted = $this->client->datastoreManager->deleteDatastore($this->workspace, $this->datastore);
        $this->assertTrue($deleted);
        $this->assertFalse($this->client->datastoreManager->datastoreExists($this->workspace, $this->datastore));
    }

    #[Test]
    public function it_can_delete_datastore_with_dot_in_name(): void
    {
        $datastore = 'phpunit_ds.dot';

        // Create workspace if not exists
        if (!$this->client->workspaceManager->workspaceExists($this->workspace)) {
            $this->client->workspaceManager->createWorkspace($this->workspace);
        }

        // Create datastore with dot
        $created = $this->client->datastoreManager->createPostGISDatastore($this->workspace, $datastore, [
            'host'     => getenv('GEOSERVER_DB_HOST')     ?: 'db',
            'port'     => getenv('GEOSERVER_DB_PORT')     ?: '5432',
            'database' => getenv('GEOSERVER_DB_NAME')     ?: 'db',
            'user'     => getenv('GEOSERVER_DB_USER')     ?: 'db',
            'passwd'   => getenv('GEOSERVER_DB_PASSWORD') ?: 'db',
        ]);

        $this->assertTrue($created);
        $this->assertTrue($this->client->datastoreManager->datastoreExists($this->workspace, $datastore));

        // Delete it
        $deleted = $this->client->datastoreManager->deleteDatastore($this->workspace, $datastore);
        $this->assertTrue($deleted);
        $this->assertFalse($this->client->datastoreManager->datastoreExists($this->workspace, $datastore));
    }

    #[Test]
    public function it_returns_false_when_deleting_dot_datastore_that_does_not_exist(): void
    {
        $datastore = 'not_existing.ds';

        $result = $this->client->datastoreManager->deleteDatastore($this->workspace, $datastore);
        $this->assertFalse($result);
    }


    #[Test]
    public function it_returns_false_when_deleting_nonexistent_datastore(): void
    {
        $result = $this->client->datastoreManager->deleteDatastore($this->workspace, 'nonexistent_ds');
        $this->assertFalse($result);
    }

    #[Test]
    public function it_throws_exception_for_invalid_workspace(): void
    {
        $this->expectException(GeoServerException::class);
        $this->client->datastoreManager->getDatastores('@@@');
    }
}
