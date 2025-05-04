<?php

namespace Hfelge\GeoServerClient;

class LayerManager
{
    public function __construct(
        protected GeoServerClient $client,
    ) {}

    public function getLayers(): array
    {
        try {
            $response = $this->client->request('GET', '/rest/layers.json');
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            throw $e;
        }
    }

    public function getLayer(string $name): array|false
    {
        try {
            $response = $this->client->request('GET', "/rest/layers/{$name}.json");
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function layerExists(string $name): bool
    {
        try {
            $this->client->request('GET', "/rest/layers/{$name}.json");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function publishLayer(string $layerName): bool
    {
        $payload = json_encode([
                                   'layer' => [
                                       'enabled' => true,
                                       'defaultStyle' => ['name' => 'default']
                                   ]
                               ]);

        try {
            $this->client->request('PUT', "/rest/layers/{$layerName}", $payload);
            return true;
        } catch (GeoServerException $e) {

            if (
                in_array($e->statusCode, [400, 404], true) ||
                ($e->statusCode === 500 && str_contains($e->getMessage(), 'because "original" is null'))
            ) {
                return false;
            }
            throw $e;
        }
    }

    public function updateLayer(string $name, array $updates): bool
    {
        $payload = json_encode(['layer' => $updates]);

        try {
            $this->client->request('PUT', "/rest/layers/{$name}", $payload);
            return true;
        } catch (GeoServerException $e) {
            if (
                in_array($e->statusCode, [400, 404], true) ||
                ($e->statusCode === 500 && str_contains($e->getMessage(), 'because "original" is null'))
            ) {
                return false;
            }
            throw $e;
        }
    }

    public function deleteLayer(string $name, bool $recurse = true): bool
    {
        $query = $recurse ? '?recurse=true' : '';

        try {
            $this->client->request('DELETE', "/rest/layers/{$name}{$query}");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }
}
