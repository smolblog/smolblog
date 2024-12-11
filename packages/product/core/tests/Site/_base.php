<?php

namespace Smolblog\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Core\Permissions\GlobalPermissionsService;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Site\Data\SiteRepo;
use Smolblog\Foundation\Service\KeypairGenerator;

abstract class SiteTestBase extends ModelTest {
	const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected GlobalPermissionsService & MockObject $globalPerms;
	protected SitePermissionsService & MockObject $sitePerms;
	protected KeypairGenerator & MockObject $keygen;
	protected SiteRepo & MockObject $repo;

	protected function createMockServices(): array {
		$this->globalPerms = $this->createMock(GlobalPermissionsService::class);
		$this->sitePerms = $this->createMock(SitePermissionsService::class);
		$this->keygen = $this->createMock(KeypairGenerator::class);
		$this->repo = $this->createMock(SiteRepo::class);

		return [
			GlobalPermissionsService::class => fn() => $this->globalPerms,
			SitePermissionsService::class => fn() => $this->sitePerms,
			KeypairGenerator::class => fn() => $this->keygen,
			SiteRepo::class => fn() => $this->repo,
			...parent::createMockServices(),
		];
	}
}
