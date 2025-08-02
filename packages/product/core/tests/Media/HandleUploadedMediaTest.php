<?php

namespace Smolblog\Core\Media\Commands;

require_once __DIR__ . '/_base.php';

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaCreated;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\MediaTestBase;

final class HandleUploadedMediaTest extends MediaTestBase {
	public function testHappyPath() {
		$mediaId = $this->randomId();
		$command = new HandleUploadedMedia(
			file: $this->createMock(UploadedFileInterface::class),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'Image for testing',
			mediaId: $mediaId,
		);
		$media = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: 'testimage.jpg',
			accessibilityText: 'Image for testing',
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: [],
		);

		$this->mockHandler->expects($this->once())
			->method('handleUploadedFile')
			->with($command, $command->mediaId)
			->willReturn($media);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$event = new MediaCreated(
			entityId: $mediaId,
			aggregateId: $command->siteId,
			userId: $command->userId,
			title: $media->title,
			accessibilityText: $command->accessibilityText,
			mediaType: $media->type,
			handler: $media->handler,
			fileDetails: []
		);
		$this->expectEvent($event);
		$this->assertObjectEquals($media, $event->getMediaObject());

		$this->app->execute($command);
	}

	public function testItRequiresAltText() {
		$this->expectException(InvalidValueProperties::class);

		new HandleUploadedMedia(
			file: $this->createMock(UploadedFileInterface::class),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: '',
		);
	}

	public function testItRequiresANonemptyTitleIfGiven() {
		$this->expectException(InvalidValueProperties::class);

		new HandleUploadedMedia(
			file: $this->createMock(UploadedFileInterface::class),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'alt text',
			title: '',
		);
	}

	public function testItFailsIfTheGivenContentIdExists() {
		$mediaId = $this->randomId();
		$command = new HandleUploadedMedia(
			file: $this->createMock(UploadedFileInterface::class),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			mediaId: $mediaId,
			accessibilityText: 'alt text',
		);

		$this->contentRepo->method('hasMediaWithId')->willReturn(true);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$this->expectException(InvalidValueProperties::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheUserCannotHandleUploadedMedia() {
		$command = new HandleUploadedMedia(
			file: $this->createMock(UploadedFileInterface::class),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			accessibilityText: 'alt text',
		);

		$this->contentRepo->method('hasMediaWithId')->willReturn(false);
		$this->perms->method('canUploadMedia')->willReturn(false);

		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItGeneratesANewIdThatDoesNotExist() {
		$command = new HandleUploadedMedia(
			file: $this->createMock(UploadedFileInterface::class),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			accessibilityText: 'alt text',
		);

		$this->mockHandler
			->method('handleUploadedFile')
			->willReturnCallback(fn($cmd, $id) => new Media(
				id: $id,
				userId: $cmd->userId,
				siteId: $cmd->siteId,
				title: 'testimage.jpg',
				accessibilityText: 'Image for testing',
				type: MediaType::Image,
				handler: 'testmock',
				fileDetails: [],
			));

		$this->contentRepo->method('hasMediaWithId')->willReturn(true, true, false);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$this->expectEventOfType(MediaCreated::class);

		$this->app->execute($command);
	}
}
