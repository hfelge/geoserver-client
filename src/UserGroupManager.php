<?php

namespace Hfelge\GeoServerClient;

class UserGroupManager
{

    public function __construct(
        protected GeoServerClient $client,
    ) {
    }

    public function getGroups() : array|false
    {
        try {
            $url      = "/rest/security/usergroup/groups.json";
            $response = $this->client->request( 'GET', $url );
            $data     = json_decode( $response['body'], TRUE );
            return $data['groups']['group'] ?? [];
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE;
            }
            throw $e;
        }
    }

    public function getGroupsForUser( string $username ) : array|false
    {
        try {
            $url      = "/rest/security/usergroup/user/" . rawurlencode( $username ) . "/groups.json";
            $response = $this->client->request( 'GET', $url );
            $data     = json_decode( $response['body'], TRUE );
            return $data['groups']['group'] ?? [];
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE;
            }
            throw $e;
        }
    }
}
