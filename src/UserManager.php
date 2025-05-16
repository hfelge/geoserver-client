<?php

namespace Hfelge\GeoServerClient;
class UserManager
{
    public function __construct(
        protected GeoServerClient $client,
    ) {}

    public function getUsers(): array|false
    {
        try {
            $response = $this->client->request('GET', '/rest/security/usergroup/users.json');

            return json_decode($response['body'], true);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function getUser(string $username): array|false
    {

        try {
            $users = $this->getUsers();

            foreach ($users['users'] as $user) {
                if (isset($user['userName']) && $user['userName'] === $username) {
                    return $user;
                }
            }

            return false;
        } catch (GeoServerException $e) {
            throw $e;
        }
    }

    public function createUser(string $username, string $password, bool $enabled = true): bool
    {
        try {
            $data = [
                'user' => [
                    'userName' => $username,
                    'password' => $password,
                    'enabled' => $enabled,
                ]
            ];

            $this->client->request('POST', '/rest/security/usergroup/users',  json_encode($data), [
                'Content-Type: application/json'
            ]);

            return true;
        } catch (GeoServerException $e) {
            throw $e;
        }
    }

    public function updateUser(string $username, bool $enabled): bool
    {
        try {
            $data = [
                'user' => [
                    'enabled' => $enabled,
                ]
            ];

            $this->client->request('POST', "/rest/security/usergroup/user/{$username}", json_encode($data), [
                'Content-Type: application/json'
            ]);

            return true;
        } catch (GeoServerException $e) {
            throw $e;
        }
    }

    public function updatePassword(string $username, string $newPassword): bool
    {
        try {
            $data = [
                'oldPassword' => '',
                'newPassword' => $newPassword
            ];

            if (method_exists($this->client, 'setImpersonationUser')) {
                $this->client->setImpersonationUser($username);
            }

            $this->client->request('PUT', '/rest/security/self/password', json_encode($data), [
                'Content-Type: application/json'
            ]);

            if (method_exists($this->client, 'clearImpersonation')) {
                $this->client->clearImpersonation();
            }

            return true;
        } catch (GeoServerException $e) {
            throw $e;
        }
    }

    public function deleteUser(string $username): bool
    {
        try {
            $this->client->request('DELETE', "/rest/security/usergroup/user/{$username}");
            return true;
        } catch (GeoServerException $e) {
            if ($e->statusCode === 404) {
                return false;
            }
            throw $e;
        }
    }
}
