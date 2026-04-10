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
use Smolblog\Core\Test\Stubs\ExampleFiles;
use Smolblog\Core\Test\Stubs\TestUploadedFileInterface;

#[AllowMockObjectsWithoutExpectations]
final class HandleUploadedMediaTest extends MediaTestBase {
	public function testHappyPath() {
		$mediaId = $this->randomId();
		$command = new HandleUploadedMedia(
			file: ExampleFiles::artemisTwoEarthsetPicture(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'Earthset over the moon as captured by Artemis II',
			mediaId: $mediaId,
		);
		$media = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: 'art002e009288orig.jpg',
			accessibilityText: 'Earthset over the moon as captured by Artemis II',
			type: MediaType::Image,
			fileDetails: ['path' => '/uploads/'],
		);

		$this->perms->method('canUploadMedia')->willReturn(true);
		$this->fileRepo->method('saveFile')->willReturn(['path' => '/uploads/']);

		$event = new MediaCreated(
			entityId: $mediaId,
			aggregateId: $command->siteId,
			userId: $command->userId,
			title: $media->title,
			accessibilityText: $command->accessibilityText,
			mediaType: $media->type,
			fileDetails: ['path' => '/uploads/'],
		);
		$this->expectEvent($event);
		$this->assertValueObjectEquals($media, $event->getMediaObject());

		$this->app->execute($command, skipSerialization: true);
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
			file: ExampleFiles::artemisTwoEarthsetPicture(),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			accessibilityText: 'alt text',
		);

		$this->contentRepo->method('hasMediaWithId')->willReturn(true, true, false);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$this->expectEventOfType(MediaCreated::class);

		$this->app->execute($command, skipSerialization: true);
	}
}
