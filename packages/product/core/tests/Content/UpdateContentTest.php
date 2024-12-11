<?php

namespace Smolblog\Core\Content\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Site\Entities\UserSitePermissions;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Test\ContentTestBase;
use Smolblog\Test\TestCustomContentExtension;
use Smolblog\Test\TestCustomContentType;
use Smolblog\Test\TestDefaultContentExtension;
use Smolblog\Test\TestDefaultContentType;
use Smolblog\Test\TestEventsContentType;
use Smolblog\Test\TestEventsContentTypeUpdated;

final class UpdateContentTest extends ContentTestBase {
	public function testTypeWithDefaultService() {
		$extensions = [
			'testdefaultext' => new TestDefaultContentExtension(metaval: 'hello'),
			'testcustomext' => new TestCustomContentExtension(metaval: 'hello'),
		];
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $userId,
			extensions: $extensions,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $command->body->with(body: 'Here I go once again with the email.'),
			siteId: $command->siteId,
			userId: $userId,
			id: $contentId,
		));

		$this->customExtensionService->expects($this->once())->method('update')->with(
			command: $command,
		);
		$event = new ContentUpdated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			contentUserId: $command->userId,
			extensions: $extensions,
		);
		$this->expectEvent($event);
		$this->assertObjectEquals(
			new Content(
				body: $command->body,
				siteId: $command->siteId,
				userId: $userId,
				id: $contentId,
				extensions: $extensions,
			),
			$event->getContentObject()
		);

		$this->app->execute($command);
	}

	public function testTypeWithDefaultServiceAndCustomEvents() {
		$extensions = [
			'testdefaultext' => new TestDefaultContentExtension(metaval: 'hello'),
			'testcustomext' => new TestCustomContentExtension(metaval: 'hello'),
		];
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: new TestEventsContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $userId,
			extensions: $extensions,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $command->body->with(body: 'Here I go once again with the email.'),
			siteId: $command->siteId,
			userId: $userId,
			id: $contentId,
		));

		$this->customExtensionService->expects($this->once())->method('update')->with(
			command: $command,
		);
		$this->expectEvent(new TestEventsContentTypeUpdated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			contentUserId: $command->userId,
			extensions: $extensions,
		));

		$this->app->execute($command);
	}

	public function testTypeWithCustomService() {
		$extensions = [
			'testdefaultext' => new TestDefaultContentExtension(metaval: 'hello'),
			'testcustomext' => new TestCustomContentExtension(metaval: 'hello'),
		];
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: new TestCustomContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $userId,
			extensions: $extensions,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $command->body->with(body: 'Here I go once again with the email.'),
			siteId: $command->siteId,
			userId: $userId,
			id: $contentId,
		));

		$this->customExtensionService->expects($this->once())->method('update')->with(
			command: $command,
		);
		$this->customContentService->expects($this->once())->method('update')->with(
			command: $command,
		);

		$this->app->execute($command);
	}

	public function testItFailsIfTheGivenContentIdDoesNotExist() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $userId
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);

		$this->expectException(EntityNotFound::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheUserIdsDoNotMatch() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $this->randomId(),
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $command->body->with(body: 'Here I go once again with the email.'),
			siteId: $command->siteId,
			userId: $command->contentUserId,
			id: $contentId,
		));
		$this->perms->method('canEditAllContent')->willReturn(false);

		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItSucceedsWithTheCorrectPermissions() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $this->randomId(),
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $command->body->with(body: 'Here I go once again with the email.'),
			siteId: $command->siteId,
			userId: $command->contentUserId,
			id: $contentId,
		));
		$this->perms->method('canEditAllContent')->willReturn(true);

		$this->expectEvent(new ContentUpdated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			contentUserId: $command->contentUserId,
		));

		$this->app->execute($command);
	}
}
