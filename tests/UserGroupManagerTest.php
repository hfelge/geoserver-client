<?php

namespace Hfelge\GeoServerClient\Tests;

class UserGroupManagerTest extends TestCaseWithGeoServerClient
{
    public function test_it_can_list_groups(): void
    {
        $groups = $this->client->userGroupManager->getGroups();
        $this->assertIsArray($groups, 'Groups response is not an array');
    }

    public function test_it_returns_false_for_group_of_nonexistent_user(): void
    {
        $result = $this->client->userGroupManager->getGroupsForUser('missing_user_' . rand(1000, 99999));
        $this->assertFalse($result, 'Expected false for non-existent user group list');
    }
}
