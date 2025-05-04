<?php

declare(strict_types = 1);

use Hfelge\GeoServerClient\GeoServerException;
use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;
use PHPUnit\Framework\Attributes\Test;

class GeoServerClientTest extends TestCaseWithGeoServerClient
{
    protected string $workspace   = 'phpunit_ws';
    protected string $datastore   = 'phpunit_ds';
    protected string $featureType = 'phpunit_layer';

    protected function tearDown() : void
    {
        $this->client->layerManager->deleteLayer( $this->featureType );
        $this->client->featureTypeManager->deleteFeatureType( $this->workspace, $this->datastore, $this->featureType );
        $this->client->datastoreManager->deleteDatastore( $this->workspace, $this->datastore );
        $this->client->workspaceManager->deleteWorkspace( $this->workspace, TRUE );
    }

    #[Test]
    public function it_initializes_all_managers() : void
    {
        $this->assertNotNull( $this->client->workspaceManager );
        $this->assertNotNull( $this->client->datastoreManager );
        $this->assertNotNull( $this->client->featureTypeManager );
        $this->assertNotNull( $this->client->layerManager );
        $this->assertNotNull( $this->client->styleManager );
    }

    #[Test]
    public function it_can_send_a_successful_request() : void
    {
        $response = $this->client->request( 'GET', '/rest/about/version.json' );

        $this->assertSame( 200, $response['status'] );
        $this->assertStringContainsString( 'Version', $response['body'] );
    }

    #[Test]
    public function it_can_detect_availability() : void
    {
        $this->assertTrue( $this->client->isAvailable() );
    }

    #[Test]
    public function it_uses_cached_availability_result() : void
    {
        $firstCheck  = $this->client->isAvailable();
        $secondCheck = $this->client->isAvailable();

        $this->assertSame( $firstCheck, $secondCheck );
    }

    #[Test]
    public function it_can_force_availability_check() : void
    {
        $check = $this->client->isAvailable( forceCheck: TRUE );
        $this->assertTrue( $check );
    }

    #[Test]
    public function it_throws_exception_on_invalid_request() : void
    {
        $this->expectException( GeoServerException::class );
        $this->expectExceptionMessage( 'GeoServer API error' );

        $this->client->request( 'GET', '/rest/this/path/does/not/exist.json' );
    }

    #[Test]
    public function it_can_publish_a_feature_layer() : void
    {
        // Ausführung
        $result = $this->client->publishFeatureLayer(
            $this->workspace,
            $this->datastore,
            [
                'name'       => $this->featureType,
                'nativeName' => $this->featureType,
                'title'      => 'PHPUnit Feature Layer',
                'srs'        => 'EPSG:4326',
            ],
            [
                'host'     => getenv( 'GEOSERVER_DB_HOST' ) ?: 'db',
                'port'     => getenv( 'GEOSERVER_DB_PORT' ) ?: '5432',
                'database' => getenv( 'GEOSERVER_DB_NAME' ) ?: 'db',
                'user'     => getenv( 'GEOSERVER_DB_USER' ) ?: 'db',
                'passwd'   => getenv( 'GEOSERVER_DB_PASSWORD' ) ?: 'db',
            ]
        );

        // Prüfung
        $this->assertTrue( $result );
        $this->assertTrue( $this->client->layerManager->layerExists( $this->featureType ) );
    }
}
