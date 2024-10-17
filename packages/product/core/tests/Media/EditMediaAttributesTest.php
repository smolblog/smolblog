<?php

namespace Smolblog\Core\Media\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaAttributesUpdated;
use Smolblog\Test\MediaTestBase;

final class EditMediaAttributesTest extends MediaTestBase {
	public function testHappyPath() {
		$mediaId = $this->randomId();
		$command = new EditMediaAttributes(
			userId: $this->randomId(),
			title: 'Another one',
			accessibilityText: 'A photo of DJ Khaled in front of a green screen.',
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

		$this->expectEvent(new MediaAttributesUpdated(
			entityId: $mediaId,
			aggregateId: $media->siteId,
			userId: $command->userId,
			title: $command->title,
			accessibilityText: $command->accessibilityText,
		));

		$this->app->execute($command);
	}
}
