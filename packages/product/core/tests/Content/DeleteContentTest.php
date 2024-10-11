<?php

namespace Smolblog\Core\Content\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Site\Entities\UserSitePermissions;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Test\ContentTestBase;
use Smolblog\Test\TestCustomContentExtension;
use Smolblog\Test\TestCustomContentType;
use Smolblog\Test\TestDefaultContentExtension;
use Smolblog\Test\TestDefaultContentType;
use Smolblog\Test\TestEventsContentType;
use Smolblog\Test\TestEventsContentTypeDeleted;

final class DeleteContentTest extends ContentTestBase {
	public function testTypeWithDefaultService() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new DeleteContent(
			userId: $userId,
			contentId: $contentId,
		);
		$content = new Content(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			id: $contentId,
			extensions: [
				'testdefaultext' => new TestDefaultContentExtension(metaval: 'hello'),
				'testcustomext' => new TestCustomContentExtension(metaval: 'hello'),
			],
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn($content);

		$this->customExtensionService->expects($this->once())->method('delete')->with(
			command: $command,
			content: $content
		);
		$this->expectEvent(new ContentDeleted(
			aggregateId: $content->siteId,
			userId: $userId,
			entityId: $contentId,
		));

		$this->app->execute($command);
	}

	public function testTypeWithDefaultServiceAndCustomEvents() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new DeleteContent(
			userId: $userId,
			contentId: $contentId,
		);
		$content = new Content(
			body: new TestEventsContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			id: $contentId,
			extensions: [
				'testdefaultext' => new TestDefaultContentExtension(metaval: 'hello'),
				'testcustomext' => new TestCustomContentExtension(metaval: 'hello'),
			],
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn($content);

		$this->customExtensionService->expects($this->once())->method('delete')->with(
			command: $command,
			content: $content
		);
		$this->expectEvent(new TestEventsContentTypeDeleted(
			aggregateId: $content->siteId,
			userId: $userId,
			entityId: $contentId,
		));

		$this->app->execute($command);
	}

	public function testTypeWithCustomService() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new DeleteContent(
			userId: $userId,
			contentId: $contentId,
		);
		$content = new Content(
			body: new TestCustomContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			id: $contentId,
			extensions: [
				'testdefaultext' => new TestDefaultContentExtension(metaval: 'hello'),
				'testcustomext' => new TestCustomContentExtension(metaval: 'hello'),
			],
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn($content);

		$this->customExtensionService->expects($this->once())->method('delete')->with(
			command: $command,
			content: $content
		);
		$this->customContentService->expects($this->once())->method('delete')->with(
			command: $command,
			content: $content
		);

		$this->app->execute($command);
	}

	public function testItFailsIfTheContentDoesNotExist() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new DeleteContent(
			userId: $userId,
			contentId: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->contentRepo->method('contentById')->willReturn(null);

		$this->expectException(EntityNotFound::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheUsersDoNotMatch() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new DeleteContent(
			userId: $userId,
			contentId: $contentId,
		);
		$content = new Content(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			id: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn($content);
		$this->perms->method('canEditAllContent')->willReturn(false);

		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItSucceedsWithTheCorrectPermissions() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new DeleteContent(
			userId: $userId,
			contentId: $contentId,
		);
		$content = new Content(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			id: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn($content);
		$this->perms->method('canEditAllContent')->willReturn(true);

		$this->expectEvent(new ContentDeleted(
			aggregateId: $content->siteId,
			userId: $userId,
			entityId: $contentId,
		));

		$this->app->execute($command);
	}
}
