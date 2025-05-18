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

    public function getRolesForUser( string $username ) : array|false
    {
        try {
            $response = $this->client->request( 'GET', '/rest/security/roles/user/' . rawurlencode( $username ) );

            return json_decode( $response['body'], TRUE );
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE; // Benutzer nicht gefunden oder keine Rollen
            }
            throw $e;
        }
    }

    public function getRolesForGroup( string $groupName ) : array|false
    {
        try {
            $response = $this->client->request( 'GET', '/rest/security/roles/group/' . rawurlencode( $groupName ) );

            return json_decode( $response['body'], TRUE );
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE; // Benutzer nicht gefunden oder keine Rollen
            }
            throw $e;
        }
    }
}
