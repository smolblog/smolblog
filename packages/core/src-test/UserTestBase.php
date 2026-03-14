<?php

namespace Smolblog\Core\Test;

use Cavatappi\Test\ModelTest;
use PHPUnit\Framework\MockObject\Stub;
use Smolblog\Core\Permissions\GlobalPermissionsService;
use Smolblog\Core\User\UserRepo;

abstract class UserTestBase extends ModelTest {
	public const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected GlobalPermissionsService&Stub $globalPerms;
	protected UserRepo&Stub $repo;

	protected function createMockServices(): array {
		$this->globalPerms = $this->createStub(GlobalPermissionsService::class);
		$this->repo = $this->createStub(UserRepo::class);

		return [
			GlobalPermissionsService::class => fn() => $this->globalPerms,
			UserRepo::class => fn() => $this->repo,
			...parent::createMockServices(),
		];
	}
}
