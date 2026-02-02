<?php

namespace Smolblog\Core\Test;

use Cavatappi\Test\ModelTest;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Core\Permissions\GlobalPermissionsService;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Site\Data\SiteRepo;

#[AllowMockObjectsWithoutExpectations]
abstract class SiteTestBase extends ModelTest {
	public const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected GlobalPermissionsService&MockObject $globalPerms;
	protected SitePermissionsService&MockObject $sitePerms;
	protected SiteRepo&MockObject $repo;

	protected function createMockServices(): array {
		$this->globalPerms = $this->createMock(GlobalPermissionsService::class);
		$this->sitePerms = $this->createMock(SitePermissionsService::class);
		$this->repo = $this->createMock(SiteRepo::class);

		return [
			GlobalPermissionsService::class => fn() => $this->globalPerms,
			SitePermissionsService::class => fn() => $this->sitePerms,
			SiteRepo::class => fn() => $this->repo,
			...parent::createMockServices(),
		];
	}
}
