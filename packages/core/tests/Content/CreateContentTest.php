<?php

namespace Smolblog\Core\Content\Commands;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Test\ContentTestBase;
use Smolblog\Core\Test\TestCustomContentExtension;
use Smolblog\Core\Test\TestCustomContentType;
use Smolblog\Core\Test\TestDefaultContentExtension;
use Smolblog\Core\Test\TestDefaultContentType;
use Smolblog\Core\Test\TestEventsContentType;
use Smolblog\Core\Test\TestEventsContentTypeCreated;

#[AllowMockObjectsWithoutExpectations]
final class CreateContentTest extends ContentTestBase {
	public function testTypeWithDefaultService() {
		$extensions = [
			new TestDefaultContentExtension(metaval: 'hello'),
			new TestCustomContentExtension(metaval: 'hello'),
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
			command: $this->valueObjectEquals($command),
			contentId: $this->uuidEquals($contentId),
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
			new TestDefaultContentExtension(metaval: 'hello'),
			new TestCustomContentExtension(metaval: 'hello'),
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
			command: $this->valueObjectEquals($command),
			contentId: $this->uuidEquals($contentId),
		);
		$event = new TestEventsContentTypeCreated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			extensions: $extensions,
		);
		$this->assertEquals(
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
			new TestDefaultContentExtension(metaval: 'hello'),
			new TestCustomContentExtension(metaval: 'hello'),
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
			command: $this->valueObjectEquals($command),
			contentId: $this->uuidEquals($contentId),
		);
		$this->customExtensionService->expects($this->once())->method('create')->with(
			command: $this->valueObjectEquals($command),
			contentId: $this->uuidEquals($contentId),
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
