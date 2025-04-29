<?php

namespace Hfelge\GeoServerClient;

class StyleManager
{
    public function __construct(
        protected GeoServerClient $client
    ) {}

    public function getStyles(): array
    {
        $response = $this->client->request('GET', '/rest/styles.json');

        if ($response['status'] !== 200) {
            throw new \RuntimeException('Failed to get styles: ' . $response['body']);
        }

        return json_decode($response['body'], true);
    }

    public function getStyle(string $styleName): array
    {
        $response = $this->client->request('GET', "/rest/styles/{$styleName}.json");

        if ($response['status'] !== 200) {
            throw new \RuntimeException('Failed to get style: ' . $response['body']);
        }

        return json_decode($response['body'], true);
    }

    public function styleExists(string $styleName): bool
    {
        $response = $this->client->request('GET', "/rest/styles/{$styleName}.json");

        if ($response['status'] === 200) {
            return true;
        }

        if ($response['status'] === 404) {
            return false;
        }

        throw new \RuntimeException('Unexpected response checking style existence: ' . $response['body']);
    }

    public function createStyle(string $name, string $sldContent): bool
    {
        $payload = [
            'file' => $sldContent
        ];

        $response = $this->client->request(
            'POST',
            '/rest/styles',
            $sldContent,
            ['Content-Type: application/vnd.ogc.sld+xml']
        );

        return $response['status'] === 201;
    }

    public function updateStyle(string $name, string $sldContent): bool
    {
        $response = $this->client->request(
            'PUT',
            "/rest/styles/{$name}",
            $sldContent,
            ['Content-Type: application/vnd.ogc.sld+xml']
        );

        return $response['status'] === 200;
    }

    public function deleteStyle(string $name): bool
    {
        $response = $this->client->request('DELETE', "/rest/styles/{$name}");

        return $response['status'] === 200;
    }
}
