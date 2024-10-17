<?php

namespace Smolblog\Core\Media\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaDeleted;
use Smolblog\Test\MediaTestBase;

final class DeleteMediaTest extends MediaTestBase {
	public function testHappyPath() {
		$mediaId = $this->randomId();
		$command = new DeleteMedia(
			userId: $this->randomId(),
			mediaId: $mediaId,
		);
		$media = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $this->randomId(),
			title: 'testimage.jpg',
			accessibilityText: 'Image for testing',
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: [],
		);

		$this->contentRepo->method('mediaById')->willReturn($media);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$this->mockHandler->expects($this->once())
			->method('deleteFile')
			->with($command, $media);

		$this->expectEvent(new MediaDeleted(
			entityId: $mediaId,
			aggregateId: $media->siteId,
			userId: $command->userId,
		));

		$this->app->execute($command);
	}
}
