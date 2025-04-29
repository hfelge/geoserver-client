<?php

namespace Hfelge\GeoServerClient;

class FeatureTypeManager
{
    public function __construct(
        protected GeoServerClient $client
    ) {}

    public function getFeatureTypes(string $workspace, string $datastore): array
    {
        $response = $this->client->request('GET', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes.json");

        if ($response['status'] !== 200) {
            throw new \RuntimeException('Failed to get feature types: ' . $response['body']);
        }

        return json_decode($response['body'], true);
    }

    public function featureTypeExists(string $workspace, string $datastore, string $featureType): bool
    {
        $response = $this->client->request('GET', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes/{$featureType}.json");

        if ($response['status'] === 200) {
            return true;
        }

        if ($response['status'] === 404) {
            return false;
        }

        throw new \RuntimeException('Unexpected response checking feature type existence: ' . $response['body']);
    }

    public function createFeatureType(string $workspace, string $datastore, array $definition): bool
    {
        $payload = json_encode([
                                   'featureType' => $definition
                               ]);

        $response = $this->client->request('POST', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes", $payload);

        return $response['status'] === 201;
    }

    public function updateFeatureType(string $workspace, string $datastore, string $featureType, array $updates): bool
    {
        $payload = json_encode([
                                   'featureType' => $updates
                               ]);

        $response = $this->client->request('PUT', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes/{$featureType}", $payload);

        return $response['status'] === 200;
    }

    public function deleteFeatureType(string $workspace, string $datastore, string $featureType): bool
    {
        $response = $this->client->request('DELETE', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes/{$featureType}");

        return $response['status'] === 200;
    }
}
