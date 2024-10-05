<?php

namespace Smolblog\Core\Content\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Site\Entities\UserSitePermissions;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Test\ContentTestBase;
use Smolblog\Test\TestCustomContentType;
use Smolblog\Test\TestDefaultContentType;
use Smolblog\Test\TestEventsContentType;
use Smolblog\Test\TestEventsContentTypeUpdated;

final class UpdateContentTest extends ContentTestBase {
	public function testTypeWithDefaultService() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $userId
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $command->body,
			siteId: $command->siteId,
			userId: $userId,
			id: $contentId,
		));

		$this->expectEvent(new ContentUpdated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			contentUserId: $command->userId,
		));

		$this->app->execute($command);
	}

	public function testTypeWithDefaultServiceAndCustomEvents() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: new TestEventsContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $userId
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $command->body,
			siteId: $command->siteId,
			userId: $userId,
			id: $contentId,
		));

		$this->expectEvent(new TestEventsContentTypeUpdated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			contentUserId: $command->userId,
		));

		$this->app->execute($command);
	}

	public function testTypeWithCustomService() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: new TestCustomContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $userId
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $command->body,
			siteId: $command->siteId,
			userId: $userId,
			id: $contentId,
		));

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
			body: $command->body,
			siteId: $command->siteId,
			userId: $command->contentUserId,
			id: $contentId,
		));
		$this->siteRepo->method('userPermissionsForSite')->willReturn(new UserSitePermissions(
			userId: $command->userId,
			siteId: $command->siteId,
			canEditAllContent: false,
		));

		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItFailsIfUserPermissionsDoNotExist() {
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
			body: $command->body,
			siteId: $command->siteId,
			userId: $command->contentUserId,
			id: $contentId,
		));
		$this->siteRepo->method('userPermissionsForSite')->willReturn(null);

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
			body: $command->body,
			siteId: $command->siteId,
			userId: $command->contentUserId,
			id: $contentId,
		));
		$this->siteRepo->method('userPermissionsForSite')->willReturn(new UserSitePermissions(
			userId: $command->userId,
			siteId: $command->siteId,
			canEditAllContent: true,
		));

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
