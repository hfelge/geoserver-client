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

            $data = json_decode( $response['body'], TRUE );

            if ( !isset( $data['groups'] ) || (isset( $data['groups'] ) && empty( $data['groups'] )) ) {
                return FALSE;
            }

            return $data;

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

            $data = json_decode( $response['body'], TRUE );

            if ( !isset( $data['groups'] ) || (isset( $data['groups'] ) && empty( $data['groups'] )) ) {
                return FALSE;
            }

            return $data;
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE;
            }
            throw $e;
        }
    }
}
