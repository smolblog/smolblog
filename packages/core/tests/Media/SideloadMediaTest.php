<?php

namespace Smolblog\Core\Media\Commands;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaCreated;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Core\Test\MediaTestBase;

#[AllowMockObjectsWithoutExpectations]
final class SideloadMediaTest extends MediaTestBase {
	public function testHappyPath() {
		$mediaId = $this->randomId();
		$command = new SideloadMedia(
			url: 'https://cdn.smol.blog/site/image.png',
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'Image for testing',
			mediaId: $mediaId,
		);
		$media = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: 'image.png',
			accessibilityText: 'Image for testing',
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: [],
		);

		$this->mockHandler->expects($this->once())
			->method('sideloadFile')
			->with($this->valueObjectEquals($command), $this->uuidEquals($mediaId))
			->willReturn($media);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$this->expectEvent(new MediaCreated(
			entityId: $mediaId,
			aggregateId: $command->siteId,
			userId: $command->userId,
			title: $media->title,
			accessibilityText: $command->accessibilityText,
			mediaType: $media->type,
			handler: $media->handler,
			fileDetails: [],
		));

		$this->app->execute($command);
	}

	public function testItRequiresAltText() {
		$this->expectException(InvalidValueProperties::class);

		new SideloadMedia(
			url: '//smol.blog/test.png',
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: '',
		);
	}

	public function testItRequiresANonemptyTitleIfGiven() {
		$this->expectException(InvalidValueProperties::class);

		new SideloadMedia(
			url: '//smol.blog/test.png',
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'alt text',
			title: '',
		);
	}
}
