<?php

namespace Hfelge\GeoServerClient;

class GeoServerClient
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct( string $baseUrl, string $username, string $password )
    {
        $this->baseUrl  = rtrim( $baseUrl, '/' );
        $this->username = $username;
        $this->password = $password;
    }

    protected function request( string $method, string $url, ?string $data = NULL, array $headers = [] ) : array
    {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $this->baseUrl . $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
        curl_setopt( $ch, CURLOPT_USERPWD, "$this->username:$this->password" );

        if ( $data !== NULL ) {
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
            $headers[] = 'Content-Type: application/json';
        }

        if ( !empty( $headers ) ) {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        }

        $response = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        if ( curl_errno( $ch ) ) {
            throw new \RuntimeException( 'Curl error: ' . curl_error( $ch ) );
        }

        curl_close( $ch );

        return [
            'status' => $httpCode,
            'body'   => $response,
        ];
    }

    public function getWorkspaces() : array
    {
        $response = $this->request( 'GET', '/rest/workspaces.json' );

        if ( $response['status'] !== 200 ) {
            throw new \RuntimeException( 'Failed to get workspaces: ' . $response['body'] );
        }

        return json_decode( $response['body'], TRUE );
    }

    public function createWorkspace( string $workspace ) : bool
    {
        $payload  = json_encode( ['workspace' => ['name' => $workspace]] );
        $response = $this->request( 'POST', '/rest/workspaces', $payload );

        return $response['status'] === 201;
    }
}
