<?php

namespace Smolblog\Core\Site\Services;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Test\SiteTestBase;

#[AllowMockObjectsWithoutExpectations]
final class SiteDataServiceTest extends SiteTestBase {
	private SiteDataService $service;

	protected function setUp(): void {
		parent::setUp();

		$this->service = $this->app->container->get(SiteDataService::class);
	}

	public function testSiteByIdWillReturnNullIfNotPermissioned() {
		$this->sitePerms->method('canManageSettings')->willReturn(false);
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->repo->expects($this->never())->method('siteById');

		$this->assertNull($this->service->siteById(siteId: $siteId, userId: $userId));
	}

	public function testContentListWillReturnAllContentIfPermissioned() {
		$this->sitePerms->method('canManageSettings')->willReturn(true);
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->repo->expects($this->once())->method('siteById')->with(siteId: $this->uuidEquals($siteId));

		$this->service->siteById(siteId: $siteId, userId: $userId);
	}
}
