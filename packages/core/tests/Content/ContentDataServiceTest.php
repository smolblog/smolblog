<?php

namespace Smolblog\Core\Content\Services;

use Cavatappi\Foundation\Fields\Markdown;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Test\ContentTestBase;
use stdClass;

#[AllowMockObjectsWithoutExpectations]
final class ContentDataServiceTest extends ContentTestBase {
	private ContentDataService $service;

	protected function setUp(): void {
		parent::setUp();

		$this->service = $this->app->container->get(ContentDataService::class);
	}

	public function testContentListWillReturnOwnContentIfNotPermissioned() {
		$this->perms->method('canEditAllContent')->willReturn(false);
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->contentRepo->expects($this->once())->method('contentList')->with(
			siteId: $this->uuidEquals($siteId),
			userId: $this->uuidEquals($userId),
		);

		$this->service->contentList(siteId: $siteId, userId: $userId);
	}

	public function testContentListWillReturnAllContentIfPermissioned() {
		$this->perms->method('canEditAllContent')->willReturn(true);
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->contentRepo->expects($this->once())->method('contentList')->with(
			siteId: $this->uuidEquals($siteId),
			userId: null,
		);

		$this->service->contentList(siteId: $siteId, userId: $userId);
	}

	public function testContentByIdWillReturnOwnContent() {
		$this->perms->method('canEditAllContent')->willReturn(false);
		$userId = $this->randomId();
		$content = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $userId,
		);

		$this->contentRepo->method('contentById')->willReturn($content);

		$result = $this->service->contentById(contentId: $content->id, userId: $userId);
		$this->assertValueObjectEquals($content, $result);
	}

	public function testContentByIdWillReturnAllContentIfPermissioned() {
		$this->perms->method('canEditAllContent')->willReturn(true);
		$userId = $this->randomId();
		$content = new Content(
			id: $this->randomId(),
			body: new Note(new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);

		$result = $this->service->contentById(contentId: $content->id, userId: $userId);
		$this->assertValueObjectEquals($content, $result);
	}

	public function testContentByIdWillReturnNullIfNotPermissionedAndNotOwnContent() {
		$this->perms->method('canEditAllContent')->willReturn(false);
		$userId = $this->randomId();
		$content = new Content(
			id: $this->randomId(),
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
