<?php

namespace Hfelge\GeoServerClient;

class WorkspaceManager
{
    public function __construct(
        protected GeoServerClient $client,
    ) {}

    public function getWorkspaces(): array
    {
        try {
            $response = $this->client->request('GET', '/rest/workspaces.json');
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            throw $e;
        }
    }

    public function getWorkspace(string $name): array|false
    {
        try {
            $response = $this->client->request('GET', "/rest/workspaces/{$name}.json");
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function workspaceExists(string $workspace): bool
    {
        try {
            $this->client->request('GET', "/rest/workspaces/{$workspace}.json");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function createWorkspace(string $workspace): bool
    {
        $payload = json_encode(['workspace' => ['name' => $workspace]]);

        try {
            $this->client->request('POST', '/rest/workspaces', $payload);
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 409) {
                return false;
            }
            throw $e;
        }
    }

    public function updateWorkspace(string $workspace, array $updates): bool
    {
        $payload = json_encode(['workspace' => $updates]);

        try {
            $this->client->request('PUT', "/rest/workspaces/{$workspace}", $payload);
            return true;
        } catch (GeoServerException $e) {
            if (in_array($e->statusCode, [400, 404], true)) {
                return false;
            }
            throw $e;
        }
    }

    public function deleteWorkspace(string $workspace, bool $recurse = false): bool
    {
        $query = $recurse ? '?recurse=true' : '';

        try {
            $this->client->request('DELETE', "/rest/workspaces/{$workspace}{$query}");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }
}
