<?php

namespace Hfelge\GeoServerClient;

class GeoServerClient
{
    public WorkspaceManager $workspaceManager;
    public DatastoreManager $datastoreManager;
    public FeatureTypeManager $featureTypeManager;
    public LayerManager $layerManager;


    public function __construct(
        protected string $baseUrl,
        protected string $username,
        protected string $password,
    ) {
        $this->baseUrl = rtrim( $baseUrl, '/' );

        $this->workspaceManager = new WorkspaceManager( $this );
        $this->datastoreManager = new DatastoreManager( $this );
        $this->featureTypeManager = new FeatureTypeManager($this);
        $this->layerManager = new LayerManager($this);
    }

    public function request( string $method, string $url, ?string $data = NULL, array $headers = [] ) : array
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
}
