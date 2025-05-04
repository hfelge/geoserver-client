<?php

declare(strict_types=1);

use Hfelge\GeoServerClient\GeoServerException;
use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;
use PHPUnit\Framework\Attributes\Test;

class GeoServerClientTest extends TestCaseWithGeoServerClient
{
    #[Test]
    public function it_initializes_all_managers(): void
    {
        $this->assertNotNull($this->client->workspaceManager);
        $this->assertNotNull($this->client->datastoreManager);
        $this->assertNotNull($this->client->featureTypeManager);
        $this->assertNotNull($this->client->layerManager);
        $this->assertNotNull($this->client->styleManager);
    }

    #[Test]
    public function it_can_send_a_successful_request(): void
    {
        $response = $this->client->request('GET', '/rest/about/version.json');

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('Version', $response['body']);
    }

    #[Test]
    public function it_can_detect_availability(): void
    {
        $this->assertTrue($this->client->isAvailable());
    }

    #[Test]
    public function it_uses_cached_availability_result(): void
    {
        $firstCheck  = $this->client->isAvailable();
        $secondCheck = $this->client->isAvailable();

        $this->assertSame($firstCheck, $secondCheck);
    }

    #[Test]
    public function it_can_force_availability_check(): void
    {
        $check = $this->client->isAvailable(forceCheck: true);
        $this->assertTrue($check);
    }

    #[Test]
    public function it_throws_exception_on_invalid_request(): void
    {
        $this->expectException(GeoServerException::class);
        $this->expectExceptionMessage('GeoServer API error');

        $this->client->request('GET', '/rest/this/path/does/not/exist.json');
    }
}
