<?php

namespace Smolblog\Core\User;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Smolblog\Core\Test\UserTestBase;

final class GrantUserSudoTest extends UserTestBase {
	public function testHappyPath() {
		$this->globalPerms->method('canGrantUserSudo')->willReturn(true);
		$this->repo->method('hasUserWithId')->willReturn(true);

		$command = new GrantUserSudo(
			userId: $this->randomId(),
			userIdToEscalate: $this->randomId(),
		);

		$this->expectEvent(
			new UserGrantedSudo(
				userId: $command->userId,
				entityId: $command->userIdToEscalate,
			),
		);
		$this->app->execute($command);
	}

	public function testItFailsIfTheUserDoesNotExist() {
		$this->globalPerms->method('canGrantUserSudo')->willReturn(true);
		$this->repo->method('hasUserWithId')->willReturn(false);

		$command = new GrantUserSudo(
			userId: $this->randomId(),
			userIdToEscalate: $this->randomId(),
		);

		$this->expectNoEvents();
		$this->expectException(EntityNotFound::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheUserIsNotAuthorized() {
		$this->globalPerms->method('canGrantUserSudo')->willReturn(false);
		$this->repo->method('hasUserWithId')->willReturn(true);

		$command = new GrantUserSudo(
			userId: $this->randomId(),
			userIdToEscalate: $this->randomId(),
		);

		$this->expectNoEvents();
		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}
}
