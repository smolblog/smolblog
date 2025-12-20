<?php

namespace Smolblog\Core\User;

use Cavatappi\Test\TestCase;

final class EntityTest extends TestCase {
	public function testAUserObjectCanBeCreated() {
		$user = new User(
			id: $this->randomId(),
			key: 'test',
			displayName: 'Test User',
			handler: 'phpunit',
		);

		$this->assertInstanceOf(User::class, $user);
	}

	public function testAnInternalSystemUserExists() {
		$smolbot = InternalSystemUser::object();

		$this->assertInstanceOf(User::class, $smolbot);
		$this->assertEquals(InternalSystemUser::ID, $smolbot->id->toString());
	}
}
