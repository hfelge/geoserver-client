<?php


namespace Hfelge\GeoServerClient;

class DatastoreManager
{
    public function __construct(
        protected GeoServerClient $client,
    ) {
    }


    public function getDatastores( string $workspace ) : array
    {
        $response = $this->client->request( 'GET', "/rest/workspaces/{$workspace}/datastores.json" );

        if ( $response['status'] !== 200 ) {
            throw new \RuntimeException( 'Failed to get datastores: ' . $response['body'] );
        }

        return json_decode( $response['body'], TRUE );
    }

    public function getDatastore(string $workspace, string $datastore): array
    {
        $response = $this->client->request('GET', "/rest/workspaces/{$workspace}/datastores/{$datastore}.json");

        if ($response['status'] !== 200) {
            throw new \RuntimeException('Failed to get datastore: ' . $response['body']);
        }

        return json_decode($response['body'], true);
    }

    public function datastoreExists( string $workspace, string $datastore ) : bool
    {
        $response = $this->client->request( 'GET', "/rest/workspaces/{$workspace}/datastores/{$datastore}.json" );

        if ( $response['status'] === 200 ) {
            return TRUE;
        }

        if ( $response['status'] === 404 ) {
            return FALSE;
        }

        throw new \RuntimeException( 'Unexpected response checking datastore existence: ' . $response['body'] );
    }

    public function createPostGISDatastore( string $workspace, string $datastore, array $connectionParameters ) : bool
    {
        $payload = json_encode( [
                                    'dataStore' => [
                                        'name'                 => $datastore,
                                        'connectionParameters' => array_merge(
                                            [
                                                'dbtype' => 'postgis',
                                            ],
                                            $connectionParameters
                                        ),
                                    ],
                                ] );

        $response = $this->client->request( 'POST', "/rest/workspaces/{$workspace}/datastores", $payload );

        return $response['status'] === 201;
    }

    public function updateDatastore( string $workspace, string $datastore, array $updates ) : bool
    {
        $payload  = json_encode( ['dataStore' => $updates] );
        $response = $this->client->request( 'PUT', "/rest/workspaces/{$workspace}/datastores/{$datastore}", $payload );

        return $response['status'] === 200;
    }

    public function deleteDatastore( string $workspace, string $datastore, bool $recurse = FALSE ) : bool
    {
        $query    = $recurse ? '?recurse=true' : '';
        $response = $this->client->request( 'DELETE', "/rest/workspaces/{$workspace}/datastores/{$datastore}{$query}" );

        return $response['status'] === 200;
    }


}
