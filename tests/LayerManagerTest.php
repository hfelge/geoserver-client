<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hfelge\GeoServerClient\GeoServerClient;
use Hfelge\GeoServerClient\LayerManager;

class LayerManagerTest extends TestCase
{
    protected LayerManager $layerManager;
    protected GeoServerClient $mockClient;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(GeoServerClient::class);
        $this->layerManager = new LayerManager($this->mockClient);
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(LayerManager::class, $this->layerManager);
    }

    #[Test]
    public function it_returns_true_if_layer_exists(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/layers/testlayer.json')
            ->willReturn(['status' => 200, 'body' => '']);

        $result = $this->layerManager->layerExists('testlayer');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_if_layer_does_not_exist(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/layers/testlayer.json')
            ->willReturn(['status' => 404, 'body' => '']);

        $result = $this->layerManager->layerExists('testlayer');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_publishes_layer_successfully(): void
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

        $result = $this->layerManager->publishLayer('testworkspace', 'teststore', 'testfeaturetype');

        $this->assertTrue($result);
    }
}
