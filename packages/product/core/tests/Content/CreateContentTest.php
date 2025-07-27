<?php

namespace Smolblog\Core\Content\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Site\Entities\UserSitePermissions;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\ContentTestBase;
use Smolblog\Test\TestCustomContentExtension;
use Smolblog\Test\TestCustomContentType;
use Smolblog\Test\TestDefaultContentExtension;
use Smolblog\Test\TestDefaultContentType;
use Smolblog\Test\TestEventsContentType;
use Smolblog\Test\TestEventsContentTypeCreated;

final class CreateContentTest extends ContentTestBase {
	public function testTypeWithDefaultService() {
		$extensions = [
			'testdefaultext' => new TestDefaultContentExtension(metaval: 'hello'),
			'testcustomext' => new TestCustomContentExtension(metaval: 'hello'),
		];
		$contentId = $this->randomId();
		$command = new CreateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
			extensions: $extensions,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->perms->method('canCreateContent')->willReturn(true);

		$this->customExtensionService->expects($this->once())->method('create')->with(
			command: $command,
			contentId: $contentId,
		);
		$this->expectEvent(new ContentCreated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			extensions: $extensions,
		));

		$this->app->execute($command);
	}

	public function testTypeWithDefaultServiceAndCustomEvents() {
		$extensions = [
			'testdefaultext' => new TestDefaultContentExtension(metaval: 'hello'),
			'testcustomext' => new TestCustomContentExtension(metaval: 'hello'),
		];
		$contentId = $this->randomId();
		$command = new CreateContent(
			body: new TestEventsContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
			extensions: $extensions,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->perms->method('canCreateContent')->willReturn(true);

		$this->customExtensionService->expects($this->once())->method('create')->with(
			command: $command,
			contentId: $contentId,
		);
		$event = new TestEventsContentTypeCreated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			extensions: $extensions,
		);
		$this->assertObjectEquals(
			new Content(
				body: $command->body,
				siteId: $command->siteId,
				userId: $command->userId,
				id: $contentId,
				extensions: $extensions,
			),
			$event->getContentObject(),
		);
		$this->expectEvent($event);

		$this->app->execute($command);
	}

	public function testTypeWithCustomService() {
		$extensions = [
			'testdefaultext' => new TestDefaultContentExtension(metaval: 'hello'),
			'testcustomext' => new TestCustomContentExtension(metaval: 'hello'),
		];
		$contentId = $this->randomId();
		$command = new CreateContent(
			body: new TestCustomContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
			extensions: $extensions,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->perms->method('canCreateContent')->willReturn(true);

		$this->customContentService->expects($this->once())->method('create')->with(
			command: $command,
			contentId: $contentId,
		);
		$this->customExtensionService->expects($this->once())->method('create')->with(
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
		$this->perms->method('canCreateContent')->willReturn(true);

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
		$this->perms->method('canCreateContent')->willReturn(false);

		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItGeneratesANewIdThatDoesNotExistOnTheThirdTry() {
		$command = new CreateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true, true, false);
		$this->perms->method('canCreateContent')->willReturn(true);

		$this->expectEventOfType(ContentCreated::class);

		$this->app->execute($command);
	}

	public function testItGeneratesANewIdThatDoesNotExistOnTheFirstTry() {
		$command = new CreateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->perms->method('canCreateContent')->willReturn(true);

		$this->expectEventOfType(ContentCreated::class);

		$this->app->execute($command);
	}
}
