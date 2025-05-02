<?php

namespace Hfelge\GeoServerClient;

class GeoServerClient
{
    public WorkspaceManager   $workspaceManager;
    public DatastoreManager   $datastoreManager;
    public FeatureTypeManager $featureTypeManager;
    public LayerManager       $layerManager;
    public StyleManager       $styleManager;


    public function __construct(
        protected string $baseUrl,
        protected string $username,
        protected string $password,
    ) {
        $this->baseUrl = rtrim( $baseUrl, '/' );

        $this->workspaceManager   = new WorkspaceManager( $this );
        $this->datastoreManager   = new DatastoreManager( $this );
        $this->featureTypeManager = new FeatureTypeManager( $this );
        $this->layerManager       = new LayerManager( $this );
        $this->styleManager       = new StyleManager( $this );
    }

    public function request( string $method, string $url, ?string $body = NULL, array $headers = [] ) : array
    {
        $ch = curl_init( $this->baseUrl . $url );

        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        curl_setopt_array( $ch, [
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CUSTOMREQUEST  => strtoupper( $method ),
            CURLOPT_USERPWD        => "{$this->username}:{$this->password}",
            CURLOPT_HTTPHEADER     => array_merge( $defaultHeaders, $headers ),
        ] );

        if ( $body !== NULL ) {
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
        }

        $responseBody = curl_exec( $ch );
        $statusCode   = curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
        $error        = curl_error( $ch );

        curl_close( $ch );

        if ( $error !== '' ) {
            throw new GeoServerException( 0, $url, "CURL error: $error" );
        }

        if ( $statusCode >= 400 ) {
            throw new GeoServerException( $statusCode, $url, $responseBody ?? '' );
        }

        return [
            'status' => $statusCode,
            'body'   => $responseBody,
        ];
    }
}
