<?php

namespace Smolblog\Core\Media\Commands;

require_once __DIR__ . '/_base.php';

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaCreated;
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

		$this->expectEvent(new MediaCreated(
			entityId: $mediaId,
			aggregateId: $command->siteId,
			userId: $command->userId,
			title: $media->title,
			accessibilityText: $command->accessibilityText,
			type: $media->type,
			handler: $media->handler,
			fileDetails: []
		));

		$this->app->execute($command);
	}
}
