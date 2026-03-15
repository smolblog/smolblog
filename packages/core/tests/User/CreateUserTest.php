<?php

namespace Smolblog\Core\User;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Core\Test\UserTestBase;

final class CreateUserTest extends UserTestBase {
	public function testHappyPath() {
		$this->globalPerms->method('canRegisterUser')->willReturn(true);
		$processUser = $this->randomId();
		$expectedUser = new User(
			id: $this->randomId(),
			key: 'testuser',
			displayName: 'Test User',
			sudo: false,
		);

		$this->expectEvent(
			new UserRegistered(
				userId: $processUser,
				entityId: $expectedUser->id,
				key: $expectedUser->key,
				displayName: $expectedUser->displayName,
			),
		);
		$result = $this->app->execute(
			new RegisterUser(
				userId: $processUser,
				key: $expectedUser->key,
				displayName: $expectedUser->displayName,
				newUserId: $expectedUser->id,
			),
		);

		$this->assertUuidEquals($expectedUser->id, $result);
	}

	public function testItFailsIfTheIdAlreadyExists() {
		$this->repo->method('hasUserWithId')->willReturn(true);

		$this->expectException(InvalidValueProperties::class);
		$this->expectNoEvents();

		$this->app->execute(
			new RegisterUser(
				userId: $this->randomId(),
				key: 'key',
				displayName: 'Display',
				newUserId: $this->randomId(),
			),
		);
	}

	public function testItFailsIfTheKeyAlreadyExists() {
		$this->repo->method('hasUserWithId')->willReturn(false);
		$this->repo->method('hasUserWithKey')->willReturn(true);

		$this->expectException(InvalidValueProperties::class);
		$this->expectNoEvents();

		$this->app->execute(
			new RegisterUser(
				userId: $this->randomId(),
				key: 'key',
				displayName: 'Display',
				newUserId: $this->randomId(),
			),
		);
	}

	public function testItFailsIfPermissionsAreNotCorrect() {
		$this->repo->method('hasUserWithId')->willReturn(false);
		$this->repo->method('hasUserWithKey')->willReturn(false);
		$this->globalPerms->method('canRegisterUser')->willReturn(false);

		$this->expectException(CommandNotAuthorized::class);
		$this->expectNoEvents();

		$this->app->execute(
			new RegisterUser(
				userId: $this->randomId(),
				key: 'key',
				displayName: 'Display',
				newUserId: $this->randomId(),
			),
		);
	}
	public function testItWillGenerateAnUnusedIdIfNoneGiven() {
		$this->globalPerms->method('canRegisterUser')->willReturn(true);
		$this->repo->method('hasUserWithId')->willReturn(true, true, false);
		$processUser = $this->randomId();

		$this->expectEventOfType(UserRegistered::class);
		$this->app->execute(
			new RegisterUser(
				userId: $processUser,
				key: 'key',
				displayName: 'Display',
			),
		);
	}
}
