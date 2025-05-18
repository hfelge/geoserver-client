<?php

namespace Hfelge\GeoServerClient;

class RoleManager
{
    public function __construct(
        protected GeoServerClient $client
    ) {}

    public function getRoles(): array|false
    {
        try {
            $response = $this->client->request('GET', '/rest/security/roles');
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function getRole(string $roleName): array|false
    {
        try {
            $response = $this->client->request('GET', '/rest/security/roles/' . rawurlencode($roleName));
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function createRole(string $roleName): bool
    {
        try {
            $data = ['roleName' => $roleName];

            $this->client->request(
                         'POST',
                         '/rest/security/roles',
                         json_encode($data),
                 ['Content-Type: application/json'],
            );

            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 405 || $e->statusCode === 409) {
                return false;
            }
            throw $e;
        }
    }

    public function deleteRole(string $roleName): bool
    {
        try {
            $this->client->request('DELETE', '/rest/security/roles/' . rawurlencode($roleName));
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function getUsersWithRole(string $roleName): array|false
    {
        try {
            $response = $this->client->request('GET', '/rest/security/roles/' . rawurlencode($roleName) . '/users');
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function getGroupsWithRole(string $roleName): array|false
    {
        try {
            $response = $this->client->request('GET', '/rest/security/roles/' . rawurlencode($roleName) . '/groups');
            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }
}
