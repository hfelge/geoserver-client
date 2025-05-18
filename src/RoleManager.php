<?php

namespace Hfelge\GeoServerClient;

class RoleManager
{
    public function __construct(
        protected GeoServerClient $client,
    ) {
    }

    public function getRoles() : array|false
    {
        try {
            $response = $this->client->request( 'GET', '/rest/security/roles' );
            return json_decode( $response['body'], TRUE );
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE;
            }
            throw $e;
        }
    }

    public function roleExists( string $roleName ) : bool
    {
        $roles = $this->getRoles();

        if ( !is_array( $roles ) || !isset( $roles['roles'] ) ) {
            return FALSE;
        }

        return in_array( $roleName, $roles['roles'], TRUE );
    }

    public function createRole( string $roleName ) : bool
    {
        try {
            $this->client->request( 'POST', '/rest/security/roles/role/' . rawurlencode( $roleName ) );
            return TRUE;
        } catch ( GeoServerException $e ) {
            if ( str_contains( $e->getMessage(), 'already exists' ) ) {
                return FALSE;
            }
            throw $e;
        }
    }

    public function deleteRole( string $roleName ) : bool
    {
        try {
            $this->client->request( 'DELETE', '/rest/security/roles/role/' . rawurlencode( $roleName ) );
            return TRUE;
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE;
            }
            throw $e;
        }
    }

    public function getUsersWithRole( string $roleName ) : array|false
    {
        try {
            $response = $this->client->request( 'GET', '/rest/security/roles/' . rawurlencode( $roleName ) . '/users' );
            return json_decode( $response['body'], TRUE );
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE;
            }
            throw $e;
        }
    }

    public function getGroupsWithRole( string $roleName ) : array|false
    {
        try {
            $response = $this->client->request( 'GET', '/rest/security/roles/' . rawurlencode( $roleName ) . '/groups' );
            return json_decode( $response['body'], TRUE );
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE;
            }
            throw $e;
        }
    }
}
