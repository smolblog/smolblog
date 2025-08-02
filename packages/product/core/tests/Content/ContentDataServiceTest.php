<?php

namespace Smolblog\Core\Content\Services;

require_once __DIR__ . '/_base.php';

use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Site\Entities\UserSitePermissions;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Test\ContentTestBase;
use Smolblog\Test\TestDefaultContentType;
use stdClass;

final class ContentDataServiceTest extends ContentTestBase {
	private ContentDataService $service;

	protected function setUp(): void
	{
		parent::setUp();

		$this->service = $this->app->container->get(ContentDataService::class);
	}

	public function testContentListWillReturnOwnContentIfNotPermissioned() {
		$this->perms->method('canEditAllContent')->willReturn(false);
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->contentRepo->expects($this->once())->method('contentList')->with(
			siteId: $siteId, userId: $userId
		);

		$this->service->contentList(siteId: $siteId, userId: $userId);
	}

	public function testContentListWillReturnAllContentIfPermissioned() {
		$this->perms->method('canEditAllContent')->willReturn(true);
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->contentRepo->expects($this->once())->method('contentList')->with(
			siteId: $siteId, userId: null
		);

		$this->service->contentList(siteId: $siteId, userId: $userId);
	}

	public function testContentByIdWillReturnOwnContent() {
		$this->perms->method('canEditAllContent')->willReturn(false);
		$userId = $this->randomId();
		$content = new Content(
			body: new Note(new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $userId,
		);

		$this->contentRepo->method('contentById')->willReturn($content);

		$result = $this->service->contentById(contentId: $content->id, userId: $userId);
		$this->assertObjectEquals($content, $result ?? new stdClass());
	}

	public function testContentByIdWillReturnAllContentIfPermissioned() {
		$this->perms->method('canEditAllContent')->willReturn(true);
		$userId = $this->randomId();
		$content = new Content(
			body: new Note(new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);

		$result = $this->service->contentById(contentId: $content->id, userId: $userId);
		$this->assertObjectEquals($content, $result ?? new stdClass());
	}

	public function testContentByIdWillReturnNullIfNotPermissionedAndNotOwnContent() {
		$this->perms->method('canEditAllContent')->willReturn(false);
		$userId = $this->randomId();
		$content = new Content(
			body: new Note(new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);

		$result = $this->service->contentById(contentId: $content->id, userId: $userId);
		$this->assertNull($result);
	}

	public function testContentByIdWillReturnNullIfContentIsNotFound() {
		$this->perms->method('canEditAllContent')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(null);

		$result = $this->service->contentById(contentId: $this->randomId(), userId: $this->randomId());
		$this->assertNull($result);
	}
}
