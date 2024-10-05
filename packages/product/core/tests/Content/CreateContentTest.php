<?php

namespace Smolblog\Core\Content\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Site\Entities\UserSitePermissions;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\ContentTestBase;
use Smolblog\Test\TestCustomContentType;
use Smolblog\Test\TestDefaultContentType;
use Smolblog\Test\TestEventsContentType;
use Smolblog\Test\TestEventsContentTypeCreated;

final class CreateContentTest extends ContentTestBase {
	public function testTypeWithDefaultService() {
		$contentId = $this->randomId();
		$command = new CreateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->siteRepo->method('userPermissionsForSite')->willReturn(new UserSitePermissions(
			userId: $command->userId,
			siteId: $command->siteId,
			canCreateContent: true,
		));

		$this->expectEvent(new ContentCreated(
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
		$command = new CreateContent(
			body: new TestEventsContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->siteRepo->method('userPermissionsForSite')->willReturn(new UserSitePermissions(
			userId: $command->userId,
			siteId: $command->siteId,
			canCreateContent: true,
		));

		$this->expectEvent(new TestEventsContentTypeCreated(
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
		$command = new CreateContent(
			body: new TestCustomContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->siteRepo->method('userPermissionsForSite')->willReturn(new UserSitePermissions(
			userId: $command->userId,
			siteId: $command->siteId,
			canCreateContent: true,
		));

		$this->customContentService->expects($this->once())->method('create')->with(
			command: $command,
			contentId: $contentId,
		);

		$this->app->execute($command);
	}

	public function testItFailsIfTheGivenContentIdExists() {
		$contentId = $this->randomId();
		$command = new CreateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->siteRepo->method('userPermissionsForSite')->willReturn(new UserSitePermissions(
			userId: $command->userId,
			siteId: $command->siteId,
			canCreateContent: true,
		));

		$this->expectException(InvalidValueProperties::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheUserCannotCreateContent() {
		$contentId = $this->randomId();
		$command = new CreateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->siteRepo->method('userPermissionsForSite')->willReturn(new UserSitePermissions(
			userId: $command->userId,
			siteId: $command->siteId,
			canCreateContent: false,
		));

		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItFailsIfUserPermissionsDoNotExist() {
		$contentId = $this->randomId();
		$command = new CreateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->siteRepo->method('userPermissionsForSite')->willReturn(null);

		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItGeneratesANewIdThatDoesNotExist() {
		$command = new CreateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true, true, false);
		$this->siteRepo->method('userPermissionsForSite')->willReturn(new UserSitePermissions(
			userId: $command->userId,
			siteId: $command->siteId,
			canCreateContent: true,
		));

		$this->mockEventBus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(ContentCreated::class));

		$this->app->execute($command);
	}
}
