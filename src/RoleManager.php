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

            $data = json_decode( $response['body'], TRUE );

            if ( !isset( $data['roles'] ) || (isset( $data['roles'] ) && empty( $data['roles'] )) ) {
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

    public function getRolesForGroup( string $groupName ) : array|false
    {
        try {
            $response = $this->client->request( 'GET', '/rest/security/roles/group/' . rawurlencode( $groupName ) );

            $data = json_decode( $response['body'], TRUE );

            if ( !isset( $data['roles'] ) || (isset( $data['roles'] ) && empty( $data['roles'] )) ) {
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

    public function assignRoleToUser( string $username, string $roleName ) : bool
    {
        try {
            $this->client->request(
                'POST',
                '/rest/security/roles/role/' . rawurlencode( $roleName ) . '/user/' . rawurlencode( $username )
            );
            return TRUE;
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE; // Benutzer oder Rolle nicht gefunden
            }
            throw $e;
        }
    }

    public function removeRoleFromUser( string $username, string $roleName ) : bool
    {
        try {
            $this->client->request(
                'DELETE',
                '/rest/security/roles/role/' . rawurlencode( $roleName ) . '/user/' . rawurlencode( $username )
            );
            return TRUE;
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE; // Benutzer oder Rolle nicht gefunden
            }
            throw $e;
        }
    }

    public function assignRoleToGroup( string $groupName, string $roleName ) : bool
    {
        try {
            $this->client->request(
                'POST',
                '/rest/security/roles/role/' . rawurlencode( $roleName ) . '/group/' . rawurlencode( $groupName )
            );
            return TRUE;
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE; // Gruppe oder Rolle nicht gefunden
            }
            throw $e;
        }
    }

    public function removeRoleFromGroup( string $groupName, string $roleName ) : bool
    {
        try {
            $this->client->request(
                'DELETE',
                '/rest/security/roles/role/' . rawurlencode( $roleName ) . '/group/' . rawurlencode( $groupName )
            );
            return TRUE;
        } catch ( GeoServerException $e ) {
            if ( $e->statusCode === 404 ) {
                return FALSE;
            }
            throw $e;
        }
    }


}
