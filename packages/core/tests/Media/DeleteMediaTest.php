<?php

namespace Smolblog\Core\Media\Commands;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaDeleted;
use Smolblog\Core\Test\MediaTestBase;

#[AllowMockObjectsWithoutExpectations]
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
			fileDetails: [],
		);

		$this->contentRepo->method('mediaById')->willReturn($media);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$this->fileRepo->expects($this->once())
			->method('deleteFile')
			->with(
				$this->valueObjectEquals($media),
			);

		$this->expectEvent(new MediaDeleted(
			entityId: $mediaId,
			aggregateId: $media->siteId,
			userId: $command->userId,
		));

		$this->app->execute($command);
	}
}
