<?php

namespace Hfelge\GeoServerClient;

class LayerManager
{
    public function __construct(
        protected GeoServerClient $client
    ) {}

    public function getLayers(): array
    {
        $response = $this->client->request('GET', '/rest/layers.json');

        if ($response['status'] !== 200) {
            throw new \RuntimeException('Failed to get layers: ' . $response['body']);
        }

        return json_decode($response['body'], true);
    }

    public function layerExists(string $layerName): bool
    {
        $response = $this->client->request('GET', "/rest/layers/{$layerName}.json");

        if ($response['status'] === 200) {
            return true;
        }

        if ($response['status'] === 404) {
            return false;
        }

        throw new \RuntimeException('Unexpected response checking layer existence: ' . $response['body']);
    }

    public function publishLayer(string $workspace, string $datastore, string $featureTypeName): bool
    {
        // Trick: In GeoServer wird ein Layer automatisch veröffentlicht,
        // sobald ein FeatureType registriert wird.
        // Hier können wir optional noch einmal sicherstellen, dass er aktiviert ist.

        $response = $this->client->request('POST', "/rest/workspaces/{$workspace}/datastores/{$datastore}/featuretypes", json_encode([
                                                                                                                                         'featureType' => [
                                                                                                                                             'name' => $featureTypeName
                                                                                                                                         ]
                                                                                                                                     ]));

        return $response['status'] === 201;
    }

    public function updateLayer(string $layerName, array $updates): bool
    {
        $payload = json_encode([
                                   'layer' => $updates
                               ]);

        $response = $this->client->request('PUT', "/rest/layers/{$layerName}", $payload);

        return $response['status'] === 200;
    }

    public function deleteLayer(string $layerName): bool
    {
        $response = $this->client->request('DELETE', "/rest/layers/{$layerName}");

        return $response['status'] === 200;
    }
}
