<?php

namespace Hfelge\GeoServerClient;

class WorkspaceManager
{
    public function __construct(
        protected GeoServerClient $client
    ) {}

    public function getWorkspaces(): array
    {
        $response = $this->client->request('GET', '/rest/workspaces.json');

        if ($response['status'] !== 200) {
            throw new \RuntimeException('Failed to get workspaces: ' . $response['body']);
        }

        return json_decode($response['body'], true);
    }

    public function getWorkspace(string $name): array
    {
        $response = $this->client->request('GET', "/rest/workspaces/{$name}.json");

        if ($response['status'] !== 200) {
            throw new \RuntimeException('Failed to get workspace: ' . $response['body']);
        }

        return json_decode($response['body'], true);
    }

    public function workspaceExists(string $workspace): bool
    {
        $response = $this->client->request('GET', "/rest/workspaces/{$workspace}.json");

        if ($response['status'] === 200) {
            return true;
        }

        if ($response['status'] === 404) {
            return false;
        }

        throw new \RuntimeException('Unexpected response checking workspace existence: ' . $response['body']);
    }

    public function createWorkspace(string $workspace): bool
    {
        $payload = json_encode(['workspace' => ['name' => $workspace]]);
        $response = $this->client->request('POST', '/rest/workspaces', $payload);

        return $response['status'] === 201;
    }

    public function updateWorkspace(string $workspace, array $updates): bool
    {
        $payload = json_encode(['workspace' => $updates]);
        $response = $this->client->request('PUT', "/rest/workspaces/{$workspace}", $payload);

        return $response['status'] === 200;
    }

    public function deleteWorkspace(string $workspace, bool $recurse = false): bool
    {
        $query = $recurse ? '?recurse=true' : '';
        $response = $this->client->request('DELETE', "/rest/workspaces/{$workspace}{$query}");

        return $response['status'] === 200;
    }
}
