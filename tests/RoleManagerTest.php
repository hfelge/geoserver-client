<?php

use Hfelge\GeoServerClient\GeoServerException;
use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;

class RoleManagerTest extends TestCaseWithGeoServerClient
{
    public function test_it_can_create_and_delete_a_role(): void
    {
        $role = 'phpunit_role_' . rand(1000, 9999);

        $created = $this->client->roleManager->createRole($role);
        $this->assertTrue($created);

        $exists = $this->client->roleManager->roleExists($role);
        $this->assertTrue($exists);

        $deleted = $this->client->roleManager->deleteRole($role);
        $this->assertTrue($deleted);

        $existsAfter = $this->client->roleManager->roleExists($role);
        $this->assertFalse($existsAfter);
    }

    public function test_it_returns_false_for_unknown_role(): void
    {
        $this->assertFalse($this->client->roleManager->roleExists('nonexistent_' . rand(10000, 99999)));
    }

    public function test_it_can_get_roles(): void
    {
        $roles = $this->client->roleManager->getRoles();
        $this->assertIsArray($roles);
        $this->assertArrayHasKey('roles', $roles);
    }

    public function test_it_can_get_and_assign_role_to_user(): void
    {
        $username = 'phpunit_user_' . rand(1000, 9999);
        $password = 'secure123';
        $role = 'phpunit_user_role_' . rand(1000, 9999);

        $this->client->userManager->createUser($username, $password);
        $this->client->roleManager->createRole($role);

        $assigned = $this->client->roleManager->assignRoleToUser($username, $role);
        $this->assertTrue($assigned);

        $roles = $this->client->roleManager->getRolesForUser($username);
        $this->assertIsArray($roles);
        $this->assertArrayHasKey('roles', $roles);
        $this->assertContains($role, $roles['roles']);

        $removed = $this->client->roleManager->removeRoleFromUser($username, $role);
        $this->assertTrue($removed);

        $this->client->userManager->deleteUser($username);
        $this->client->roleManager->deleteRole($role);
    }

    public function test_it_returns_false_for_user_roles_if_user_does_not_exist(): void
    {
        $result = $this->client->roleManager->getRolesForUser('missing_user_' . rand(1000, 99999));
        $this->assertFalse($result);
    }

    public function test_it_can_get_and_assign_role_to_group(): void
    {
        $this->markTestIncomplete('Not yet implemented');

        $group = 'phpunit_group_' . rand(1000, 9999);
        $role = 'phpunit_group_role_' . rand(1000, 9999);

        $this->client->groupManager->createGroup($group);
        $this->client->roleManager->createRole($role);

        $assigned = $this->client->roleManager->assignRoleToGroup($group, $role);
        $this->assertTrue($assigned);

        $roles = $this->client->roleManager->getRolesForGroup($group);
        $this->assertIsArray($roles);
        $this->assertArrayHasKey('roles', $roles);
        $this->assertContains($role, $roles['roles']);

        $removed = $this->client->roleManager->removeRoleFromGroup($group, $role);
        $this->assertTrue($removed);

        $this->client->groupManager->deleteGroup($group);
        $this->client->roleManager->deleteRole($role);
    }

    public function test_it_returns_false_for_group_roles_if_group_does_not_exist(): void
    {
        $result = $this->client->roleManager->getRolesForGroup('missing_group_' . rand(1000, 99999));
        $this->assertFalse($result);
    }
}
