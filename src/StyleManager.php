<?php

namespace Hfelge\GeoServerClient;

class StyleManager
{
    public function __construct(
        protected GeoServerClient $client,
    ) {}

    public function getStyles(): array
    {
        try {
            $response = $this->client->request('GET', '/rest/styles.json');
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            throw $e;
        }
    }

    public function getStyle(string $styleName): array|false
    {
        try {
            $response = $this->client->request('GET', "/rest/styles/{$styleName}.json");
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function styleExists(string $styleName): bool
    {
        try {
            $this->client->request('GET', "/rest/styles/{$styleName}.json");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function styleExistsInWorkspace(string $workspace, string $styleName): bool
    {
        try {
            $this->client->request('GET', "/rest/workspaces/{$workspace}/styles/{$styleName}.sld");
            return true;
        } catch (GeoServerException $e) {

            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function createWorkspaceStyle(string $workspace, string $styleName, string $sldContent): bool
    {
        try {
            $this->client->request(
                'POST',
                "/rest/workspaces/{$workspace}/styles",
                $sldContent,
                [
                    'Content-Type: application/vnd.ogc.sld+xml',
                    'Slug: ' . $styleName . '.sld'
                ]
            );
            return true;
        } catch (GeoServerException $e) {
            if (
                $e->statusCode === 409 ||
                ($e->statusCode === 403 && str_contains($e->getMessage(), 'already exists'))
            ) {
                return false;
            }
            throw $e;
        }
    }

    public function assignStyleToLayer(string $layerName, string $styleName): bool
    {
        try {
            $this->client->request(
                'POST',
                "/rest/layers/{$layerName}/styles",
                json_encode([
                                'style' => [
                                    'name' => $styleName
                                ]
                            ]),
                ['Content-Type: application/json']
            );
            return true;
        } catch (GeoServerException $e) {
            if (in_array($e->statusCode, [400, 404, 406], true)) {
                return false;
            }
            throw $e;
        }
    }

    public function updateStyle(string $name, string $sldContent): bool
    {
        try {
            $this->client->request(
                'PUT',
                "/rest/styles/{$name}",
                $sldContent,
                ['Content-Type: application/vnd.ogc.sld+xml']
            );
            return true;
        } catch (GeoServerException $e) {
            if (
                in_array($e->statusCode, [400, 404], true) ||
                str_contains($e->getMessage(), 'getResource() because "original" is null')
            ) {
                return false;
            }
            throw $e;
        }
    }

    public function deleteStyle(string $name): bool
    {
        try {
            $this->client->request('DELETE', "/rest/styles/{$name}");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }
}
