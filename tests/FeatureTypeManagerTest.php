<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hfelge\GeoServerClient\GeoServerClient;
use Hfelge\GeoServerClient\FeatureTypeManager;

class FeatureTypeManagerTest extends TestCase
{
    protected FeatureTypeManager $featureTypeManager;
    protected GeoServerClient $mockClient;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(GeoServerClient::class);
        $this->featureTypeManager = new FeatureTypeManager($this->mockClient);
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(FeatureTypeManager::class, $this->featureTypeManager);
    }

    #[Test]
    public function it_returns_true_if_featuretype_exists(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/workspaces/testworkspace/datastores/teststore/featuretypes/testfeaturetype.json')
            ->willReturn(['status' => 200, 'body' => '']);

        $result = $this->featureTypeManager->featureTypeExists('testworkspace', 'teststore', 'testfeaturetype');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_if_featuretype_does_not_exist(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/workspaces/testworkspace/datastores/teststore/featuretypes/testfeaturetype.json')
            ->willReturn(['status' => 404, 'body' => '']);

        $result = $this->featureTypeManager->featureTypeExists('testworkspace', 'teststore', 'testfeaturetype');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_creates_featuretype_successfully(): void
    {
        $this->mockClient->method('request')
            ->with(
                'POST',
                '/rest/workspaces/testworkspace/datastores/teststore/featuretypes',
                $this->callback(function ($payload) {
                    $data = json_decode($payload, true);
                    return isset($data['featureType']['name']);
                })
            )
            ->willReturn(['status' => 201, 'body' => '']);

        $result = $this->featureTypeManager->createFeatureType('testworkspace', 'teststore', [
            'name' => 'stadtgrenzen',
            'nativeName' => 'stadtgrenzen',
            'title' => 'Stadtgrenzen',
            'srs' => 'EPSG:4326'
        ]);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_featuretype_data(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/workspaces/testworkspace/datastores/teststore/featuretypes/testfeaturetype.json')
            ->willReturn(['status' => 200, 'body' => json_encode(['featureType' => ['name' => 'testfeaturetype']])]);

        $featureType = $this->featureTypeManager->getFeatureType('testworkspace', 'teststore', 'testfeaturetype');

        $this->assertEquals('testfeaturetype', $featureType['featureType']['name']);
    }

}
