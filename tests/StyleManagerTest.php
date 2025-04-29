<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hfelge\GeoServerClient\GeoServerClient;
use Hfelge\GeoServerClient\StyleManager;

class StyleManagerTest extends TestCase
{
    protected StyleManager $styleManager;
    protected GeoServerClient $mockClient;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(GeoServerClient::class);
        $this->styleManager = new StyleManager($this->mockClient);
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(StyleManager::class, $this->styleManager);
    }

    #[Test]
    public function it_returns_true_if_style_exists(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/styles/teststyle.json')
            ->willReturn(['status' => 200, 'body' => '']);

        $result = $this->styleManager->styleExists('teststyle');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_if_style_does_not_exist(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/styles/teststyle.json')
            ->willReturn(['status' => 404, 'body' => '']);

        $result = $this->styleManager->styleExists('teststyle');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_creates_style_successfully(): void
    {
        $this->mockClient->method('request')
            ->with(
                'POST',
                '/rest/styles',
                $this->callback(function ($payload) {
                    return is_string($payload) && str_contains($payload, '<StyledLayerDescriptor');
                }),
                ['Content-Type: application/vnd.ogc.sld+xml']
            )
            ->willReturn(['status' => 201, 'body' => '']);

        $sldContent = '<StyledLayerDescriptor>...</StyledLayerDescriptor>';

        $result = $this->styleManager->createStyle('teststyle', $sldContent);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_style_data(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/styles/teststyle.json')
            ->willReturn(['status' => 200, 'body' => json_encode(['style' => ['name' => 'teststyle']])]);

        $style = $this->styleManager->getStyle('teststyle');

        $this->assertEquals('teststyle', $style['style']['name']);
    }

}
