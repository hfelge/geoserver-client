<?php

namespace Hfelge\GeoServerClient;

class FeatureTypeManager
{
    public function __construct(
        protected GeoServerClient $client,
    ) {}

    public function getFeatureTypes(string $workspace, string $datastore): array
    {
        try {
            $response = $this->client->request('GET', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes.json");
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            throw $e;
        }
    }

    public function getFeatureType(string $workspace, string $datastore, string $name): array|false
    {
        try {
            $response = $this->client->request('GET', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes/{$name}.json");
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function featureTypeExists(string $workspace, string $datastore, string $name): bool
    {
        try {
            $this->client->request('GET', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes/{$name}.json");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function createFeatureType(string $workspace, string $datastore, array $definition): bool
    {
        $payload = json_encode(['featureType' => $definition]);

        try {
            $this->client->request('POST', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes", $payload);
            return true;
        } catch (GeoServerException $e) {
            if (
                $e->statusCode === 409 ||
                ($e->statusCode === 500 && str_contains($e->getMessage(), 'already exists'))
            ) {
                return false;
            }
            throw $e;
        }
    }

    public function updateFeatureType(string $workspace, string $datastore, string $name, array $updates): bool
    {
        $payload = json_encode(['featureType' => $updates]);

        try {
            $this->client->request('PUT', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes/{$name}", $payload);
            return true;
        } catch (GeoServerException $e) {
            if (in_array($e->statusCode, [400, 404], true)) {
                return false;
            }
            throw $e;
        }
    }

    public function deleteFeatureType(string $workspace, string $datastore, string $name, bool $recurse = true): bool
    {
        $query = $recurse ? '?recurse=true' : '';

        try {
            $this->client->request('DELETE', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes/{$name}{$query}");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }
}
