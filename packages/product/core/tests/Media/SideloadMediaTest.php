<?php

namespace Smolblog\Core\Media\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaCreated;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\MediaTestBase;

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
			->with($command, $command->mediaId)
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
			fileDetails: []
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
