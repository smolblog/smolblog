<?php

namespace Smolblog\Core\Content\Commands;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Test\ContentTestBase;
use Smolblog\Core\Test\TestCustomContentExtension;
use Smolblog\Core\Test\TestCustomContentType;
use Smolblog\Core\Test\TestDefaultContentExtension;
use Smolblog\Core\Test\TestDefaultContentType;
use Smolblog\Core\Test\TestEventsContentType;
use Smolblog\Core\Test\TestEventsContentTypeUpdated;

#[AllowMockObjectsWithoutExpectations]
final class UpdateContentTest extends ContentTestBase {
	public function testTypeWithDefaultService() {
		$extensions = [
			new TestDefaultContentExtension(metaval: 'hello'),
			new TestCustomContentExtension(metaval: 'hello'),
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
			command: $this->valueObjectEquals($command),
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
		$this->assertEquals(
			new Content(
				body: $command->body,
				siteId: $command->siteId,
				userId: $userId,
				id: $contentId,
				extensions: $extensions,
			),
			$event->getContentObject(),
		);

		$this->app->execute($command);
	}

	public function testTypeWithDefaultServiceAndCustomEvents() {
		$extensions = [
			new TestDefaultContentExtension(metaval: 'hello'),
			new TestCustomContentExtension(metaval: 'hello'),
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
			command: $this->valueObjectEquals($command),
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
			new TestDefaultContentExtension(metaval: 'hello'),
			new TestCustomContentExtension(metaval: 'hello'),
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
			command: $this->valueObjectEquals($command),
		);
		$this->customContentService->expects($this->once())->method('update')->with(
			command: $this->valueObjectEquals($command),
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
			contentUserId: $userId,
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
