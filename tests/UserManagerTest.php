<?php

use Hfelge\GeoServerClient\GeoServerException;
use PHPUnit\Framework\Attributes\Test;
use Hfelge\GeoServerClient\Tests\TestCaseWithGeoServerClient;

class UserManagerTest extends TestCaseWithGeoServerClient
{
    #[Test]
    public function it_can_create_and_delete_a_user(): void
    {
        $username = 'phpunit_user';
        $password = 'secure123';

        $created = $this->client->userManager->createUser($username, $password);
        $this->assertTrue($created, 'Benutzer sollte erfolgreich erstellt werden');

        $user = $this->client->userManager->getUser($username);
        $this->assertIsArray($user, 'Benutzer sollte gefunden werden');

        $deleted = $this->client->userManager->deleteUser($username);
        $this->assertTrue($deleted, 'Benutzer sollte erfolgreich gelöscht werden');

        $userAfterDelete = $this->client->userManager->getUser($username);
        $this->assertFalse($userAfterDelete, 'Benutzer sollte nach Löschung nicht mehr existieren');
    }

    #[Test]
    public function it_can_list_users(): void
    {
        $users = $this->client->userManager->getUsers();

        $this->assertIsArray($users, 'Benutzerliste sollte ein Array sein');
    }

    #[Test]
    public function it_returns_false_for_non_existing_user(): void
    {
        $user = $this->client->userManager->getUser('non_existing_user_xyz');
        $this->assertFalse($user, 'Nicht vorhandener Benutzer sollte false liefern');
    }

    #[Test]
    public function it_can_update_user_status(): void
    {
        $username = 'phpunit_user2';
        $password = 'secure123';

        $this->client->userManager->createUser($username, $password, false);

        $updated = $this->client->userManager->updateUser($username, true);
        $this->assertTrue($updated, 'Benutzer sollte aktiviert werden können');

        $user = $this->client->userManager->getUser($username);

        $this->assertIsArray($user);
        $this->assertEquals(true, $user['enabled'], 'Benutzer sollte als aktiviert erscheinen');

        $this->client->userManager->deleteUser($username);
    }

    #[Test]
    public function it_returns_false_when_deleting_non_existing_user(): void
    {
        $deleted = $this->client->userManager->deleteUser('non_existing_user_xyz');
        $this->assertFalse($deleted, 'Löschen eines nicht existierenden Benutzers sollte false liefern');
    }

    #[Test]
    public function it_can_update_user_password(): void
    {
        $username = 'phpunit_user3';
        $password = 'start123';
        $newPassword = 'new456';

        $this->client->userManager->createUser($username, $password);

        try {
            $result = $this->client->userManager->updatePassword($username, $newPassword);
            $this->assertTrue($result, 'Passwort sollte erfolgreich aktualisiert werden');
        } catch (GeoServerException $e) {
            $this->markTestIncomplete('Passwortänderung evtl. nicht erlaubt oder nicht konfiguriert.');
        }

        $this->client->userManager->deleteUser($username);
    }
}
