<?php

declare(strict_types=1);

use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;
use Hfelge\GeoServerClient\GeoServerException;
use PHPUnit\Framework\Attributes\Test;

class WorkspaceManagerTest extends TestCaseWithGeoServerClient
{
    protected string $workspace = 'phpunit_ws';

    protected function tearDown(): void
    {
        if ($this->client->workspaceManager->workspaceExists($this->workspace)) {
            $this->client->workspaceManager->deleteWorkspace($this->workspace, true);
        }
    }

    #[Test]
    public function it_can_list_all_workspaces(): void
    {
        $result = $this->client->workspaceManager->getWorkspaces();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('workspaces', $result);
    }

    #[Test]
    public function it_can_create_a_workspace(): void
    {
        $success = $this->client->workspaceManager->createWorkspace($this->workspace);
        $this->assertTrue($success);
        $this->assertTrue($this->client->workspaceManager->workspaceExists($this->workspace));
    }

    #[Test]
    public function it_returns_false_when_creating_duplicate_workspace(): void
    {
        $this->client->workspaceManager->createWorkspace($this->workspace);
        $success = $this->client->workspaceManager->createWorkspace($this->workspace);
        $this->assertFalse($success);
    }

    #[Test]
    public function it_can_get_a_specific_workspace(): void
    {
        $this->client->workspaceManager->createWorkspace($this->workspace);
        $ws = $this->client->workspaceManager->getWorkspace($this->workspace);
        $this->assertEquals($this->workspace, $ws['workspace']['name']);
    }

    #[Test]
    public function it_returns_false_if_workspace_does_not_exist(): void
    {
        $exists = $this->client->workspaceManager->workspaceExists('nonexistent_ws');
        $this->assertFalse($exists);

        $ws = $this->client->workspaceManager->getWorkspace('nonexistent_ws');
        $this->assertFalse($ws);
    }

    #[Test]
    public function it_can_update_a_workspace(): void
    {
        $this->client->workspaceManager->createWorkspace($this->workspace);
        $result = $this->client->workspaceManager->updateWorkspace($this->workspace, ['name' => $this->workspace]);
        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_updating_non_existing_workspace(): void
    {
        $result = $this->client->workspaceManager->updateWorkspace('nonexistent_ws', ['name' => 'whatever']);
        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_delete_a_workspace(): void
    {
        $this->client->workspaceManager->createWorkspace($this->workspace);
        $result = $this->client->workspaceManager->deleteWorkspace($this->workspace);
        $this->assertTrue($result);
        $this->assertFalse($this->client->workspaceManager->workspaceExists($this->workspace));
    }

    #[Test]
    public function it_returns_false_when_deleting_non_existing_workspace(): void
    {
        $result = $this->client->workspaceManager->deleteWorkspace('nonexistent_ws');
        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_delete_a_workspace_recursively(): void
    {
        $this->client->workspaceManager->createWorkspace($this->workspace);
        $result = $this->client->workspaceManager->deleteWorkspace($this->workspace, true);
        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_for_invalid_workspace_name(): void
    {
        $result = $this->client->workspaceManager->getWorkspace('@@@');
        $this->assertFalse($result);
    }
}
