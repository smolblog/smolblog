<?php

namespace Smolblog\Core\Site\Services;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Test\SiteTestBase;
use stdClass;

final class SiteDataServiceTest extends SiteTestBase {
	private SiteDataService $service;

	protected function setUp(): void
	{
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

		$this->repo->expects($this->once())->method('siteById')->with(siteId: $siteId);

		$this->service->siteById(siteId: $siteId, userId: $userId);
	}
}
