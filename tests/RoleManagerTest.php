<?php

use Hfelge\GeoServerClient\GeoServerException;
use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;

class RoleManagerTest extends TestCaseWithGeoServerClient
{
    public function test_it_can_create_and_delete_a_role(): void
    {
        $roleName = 'phpunit_role_' . rand(1000, 9999);

        try {
            $created = $this->client->roleManager->createRole($roleName);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 405) {
                $this->markTestSkipped('Role creation is not allowed (405), possibly due to missing plugin.');
                return;
            }
            throw $e;
        }

        $this->assertTrue($created);

        $fetched = $this->client->roleManager->getRole($roleName);
        $this->assertIsArray($fetched);
        $this->assertEquals($roleName, $fetched['roleName']);

        $deleted = $this->client->roleManager->deleteRole($roleName);
        $this->assertTrue($deleted);
    }


    public function test_it_returns_false_when_deleting_nonexistent_role(): void
    {
        $deleted = $this->client->roleManager->deleteRole('nonexistent_' . rand(10000, 99999));
        $this->assertFalse($deleted);
    }

    public function test_it_returns_false_when_getting_unknown_role(): void
    {
        $result = $this->client->roleManager->getRole('unknown_' . rand(1000, 9999));
        $this->assertFalse($result);
    }

    public function test_it_can_list_roles(): void
    {
        $roles = $this->client->roleManager->getRoles();
        $this->assertIsArray($roles);
        $this->assertArrayHasKey('roles', $roles);
    }

    public function test_it_can_get_users_with_a_role(): void
    {
        $roleName = 'phpunit_role_' . rand(1000, 9999);

        try {
            $this->client->roleManager->createRole($roleName);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 405) {
                $this->markTestSkipped('Role creation not allowed (405), skipping test.');
                return;
            }
            throw $e;
        }

        $result = $this->client->roleManager->getUsersWithRole($roleName);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('users', $result);

        $this->client->roleManager->deleteRole($roleName);
    }

    public function test_it_can_get_groups_with_a_role(): void
    {
        $roleName = 'phpunit_group_role_' . rand(1000, 9999);

        try {
            $this->client->roleManager->createRole($roleName);
        } catch (GeoServerException $e) {
            if ($e->statusCode === 405) {
                $this->markTestSkipped('Role creation not allowed (405), skipping test.');
                return;
            }
            throw $e;
        }

        $result = $this->client->roleManager->getGroupsWithRole($roleName);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('groups', $result);

        $this->client->roleManager->deleteRole($roleName);
    }
}
