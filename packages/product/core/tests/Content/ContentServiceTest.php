<?php

namespace Smolblog\Core\Content\Services;

require_once __DIR__ . '/_base.php';

use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Site\Entities\UserSitePermissions;
use Smolblog\Test\ContentTestBase;
use Smolblog\Test\TestDefaultContentType;

final class ContentServiceTest extends ContentTestBase {
	private ContentService $service;

	protected function setUp(): void
	{
		parent::setUp();

		$this->service = $this->app->container->get(ContentService::class);
	}

	#[TestDox('::userCanEditContent will return false if the content does not exist')]
	public function testEditContentNotFound() {
		$contentId = $this->randomId();
		$userId = $this->randomId();

		$this->contentRepo->method('contentById')->willReturn(null);
		$this->perms->method('canEditAllContent')->willReturn(true);

		$this->assertFalse($this->service->userCanEditContent(userId: $userId, contentId: $contentId));
	}

	#[TestDox('::userCanEditContent will return true if the content user matches the user')]
	public function testEditContentMatchesId() {
		$userId = $this->randomId();
		$content = new Content(
			body: new TestDefaultContentType(title: 'One', body: 'Two'),
			siteId: $this->randomId(),
			userId: $userId,
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->perms->method('canEditAllContent')->willReturn(false);

		$this->assertTrue($this->service->userCanEditContent(userId: $userId, contentId: $content->id));
	}

	#[TestDox('::userCanEditContent will return true if the user can edit all content')]
	public function testEditContentHasPermission() {
		$userId = $this->randomId();
		$content = new Content(
			body: new TestDefaultContentType(title: 'One', body: 'Two'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->perms->method('canEditAllContent')->willReturn(true);

		$this->assertTrue($this->service->userCanEditContent(userId: $userId, contentId: $content->id));
	}

	#[TestDox('::userCanEditContent will return false if the user does not match and cannot edit all content')]
	public function testEditContentLacksPermission() {
		$userId = $this->randomId();
		$content = new Content(
			body: new TestDefaultContentType(title: 'One', body: 'Two'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->perms->method('canEditAllContent')->willReturn(false);

		$this->assertfalse($this->service->userCanEditContent(userId: $userId, contentId: $content->id));
	}
}
