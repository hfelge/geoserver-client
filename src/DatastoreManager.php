<?php

namespace Hfelge\GeoServerClient;

class DatastoreManager
{
    public function __construct(
        protected GeoServerClient $client,
    ) {}

    public function getDatastores(string $workspace): array
    {
        try {
            $response = $this->client->request('GET', "/rest/workspaces/{$workspace}/datastores.json");
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            throw $e;
        }
    }

    public function getDatastore(string $workspace, string $datastore): array|false
    {
        try {
            $response = $this->client->request('GET', "/rest/workspaces/{$workspace}/datastores/{$datastore}.json");
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function datastoreExists(string $workspace, string $datastore): bool
    {
        try {
            $this->client->request('GET', "/rest/workspaces/{$workspace}/datastores/{$datastore}.json");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function createPostGISDatastore(string $workspace, string $datastore, array $connectionParameters): bool
    {
        $payload = json_encode([
                                   'dataStore' => [
                                       'name' => $datastore,
                                       'connectionParameters' => array_merge(
                                           ['dbtype' => 'postgis'],
                                           $connectionParameters
                                       ),
                                   ]
                               ]);

        try {
            $this->client->request('POST', "/rest/workspaces/{$workspace}/datastores", $payload);
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


    public function updateDatastore(string $workspace, string $datastore, array $updates): bool
    {
        $payload = json_encode(['dataStore' => $updates]);

        try {
            $this->client->request('PUT', "/rest/workspaces/{$workspace}/datastores/{$datastore}", $payload);
            return true;
        } catch (GeoServerException $e) {
            if (in_array($e->statusCode, [400, 404], true)) {
                return false;
            }
            throw $e;
        }
    }

    public function deleteDatastore(string $workspace, string $datastore, bool $recurse = false): bool
    {
        $datastorePath = str_contains($datastore, '.') ? "{$datastore}.xml" : $datastore;
        $query = $recurse ? '?recurse=true' : '';

        try {
            $this->client->request(
                'DELETE',
                "/rest/workspaces/{$workspace}/datastores/{$datastorePath}{$query}"
            );

            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }
}
