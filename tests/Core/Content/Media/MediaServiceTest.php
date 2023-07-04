<?php

namespace Smolblog\Core\Content\Media;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Test\EventComparisonTestKit;
use Smolblog\Test\TestCase;

final class MediaServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesUploadedMedia() {
		$command = new HandleUploadedMedia(
			file: $this->createStub(UploadedFileInterface::class),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);
		$media = new Media(
			id: $this->randomId(),
			userId: $command->userId,
			siteId: $command->siteId,
			title: 'IMG_90108',
			accessibilityText: 'A small dock looking over a river.',
			type: MediaType::Image,
			handler: 'default',
			info: ['key' => 'value'],
		);

		$handler = $this->createMock(MediaHandler::class);
		$handler->expects($this->once())->method('handleUploadedFile')->with(
			$this->equalTo($command->file),
			$this->equalTo($command->userId),
			$this->equalTo($command->siteId),
		)->willReturn($media);

		$registry = $this->createStub(MediaHandlerRegistry::class);
		$registry->method('get')->willReturn($handler);

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo(
			new MediaAdded(
				contentId: $media->id,
				userId: $command->userId,
				siteId: $command->siteId,
				title: 'IMG_90108',
				accessibilityText: 'A small dock looking over a river.',
				type: MediaType::Image,
				handler: 'default',
				info: ['key' => 'value'],
			)
		));

		$service = new MediaService($bus, $registry);
		$service->onHandleUploadedMedia($command);

		$this->assertEquals($media, $command->createdMedia);
	}
}
