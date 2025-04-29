<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hfelge\GeoServerClient\GeoServerClient;
use Hfelge\GeoServerClient\WorkspaceManager;

class WorkspaceManagerTest extends TestCase
{
    protected WorkspaceManager $workspaceManager;
    protected GeoServerClient $mockClient;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(GeoServerClient::class);
        $this->workspaceManager = new WorkspaceManager($this->mockClient);
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(WorkspaceManager::class, $this->workspaceManager);
    }

    #[Test]
    public function it_returns_true_if_workspace_exists(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/workspaces/testworkspace.json')
            ->willReturn(['status' => 200, 'body' => '']);

        $result = $this->workspaceManager->workspaceExists('testworkspace');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_if_workspace_does_not_exist(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/workspaces/testworkspace.json')
            ->willReturn(['status' => 404, 'body' => '']);

        $result = $this->workspaceManager->workspaceExists('testworkspace');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_workspace_data(): void
    {
        $this->mockClient->method('request')
            ->with('GET', '/rest/workspaces/testworkspace.json')
            ->willReturn(['status' => 200, 'body' => json_encode(['workspace' => ['name' => 'testworkspace']])]);

        $workspace = $this->workspaceManager->getWorkspace('testworkspace');

        $this->assertEquals('testworkspace', $workspace['workspace']['name']);
    }
}
