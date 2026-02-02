<?php

namespace Smolblog\Core\Media\Commands;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaCreated;
use Smolblog\Core\Test\MediaTestBase;

final class TestUploadedFileInterface implements UploadedFileInterface {
	public function getStream(): StreamInterface {
		return new Stream('');
	}
	public function moveTo(string $targetPath): void {}
	public function getSize(): ?int {
		return null;
	}
	public function getError(): int {
		return \UPLOAD_ERR_OK;
	}
	public function getClientFilename(): ?string {
		return null;
	}
	public function getClientMediaType(): ?string {
		return null;
	}
}

#[AllowMockObjectsWithoutExpectations]
final class HandleUploadedMediaTest extends MediaTestBase {
	public function testHappyPath() {
		$mediaId = $this->randomId();
		$command = new HandleUploadedMedia(
			file: new TestUploadedFileInterface(),
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
			->with($this->valueObjectEquals($command), $this->uuidEquals($mediaId))
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
			fileDetails: [],
		);
		$this->expectEvent($event);
		$this->assertValueObjectEquals($media, $event->getMediaObject());

		$this->app->execute($command);
	}

	public function testItRequiresAltText() {
		$this->expectException(InvalidValueProperties::class);

		new HandleUploadedMedia(
			file: new TestUploadedFileInterface(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: '',
		);
	}

	public function testItRequiresANonemptyTitleIfGiven() {
		$this->expectException(InvalidValueProperties::class);

		new HandleUploadedMedia(
			file: new TestUploadedFileInterface(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'alt text',
			title: '',
		);
	}

	public function testItFailsIfTheGivenContentIdExists() {
		$mediaId = $this->randomId();
		$command = new HandleUploadedMedia(
			file: new TestUploadedFileInterface(),
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
			file: new TestUploadedFileInterface(),
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
			file: new TestUploadedFileInterface(),
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
