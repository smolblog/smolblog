<?php

namespace Smolblog\Core\Content\Media;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Test\EventComparisonTestKit;
use Smolblog\Test\TestCase;

final class MediaServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesUploadedMediaWithSaneDefaults() {
		$file = $this->createStub(UploadedFileInterface::class);
		$file->method('getClientFilename')->willReturn('IMG_90108.jpg');

		$fileInfo = new MediaFile(
			id: $this->randomId(),
			handler: 'default',
			details: ['key' => 'value'],
		);

		$command = new HandleUploadedMedia(
			file: $file,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$handler = $this->createMock(MediaHandler::class);
		$handler->expects($this->once())->method('handleUploadedFile')->with(
			$this->equalTo($command->file),
			$this->equalTo($command->userId),
			$this->equalTo($command->siteId),
		)->willReturn($fileInfo);

		$registry = $this->createStub(MediaHandlerRegistry::class);
		$registry->method('get')->willReturn($handler);

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(MediaAdded::class));

		$service = new MediaService($bus, $registry);
		$service->onHandleUploadedMedia($command);


		$media = new Media(
			id: $command->createdMedia?->id,
			userId: $command->userId,
			siteId: $command->siteId,
			title: 'IMG_90108.jpg',
			accessibilityText: 'Uploaded file',
			type: MediaType::Image,
			handler: 'default',
			info: ['key' => 'value'],
		);

		$this->assertEquals($media, $command->createdMedia);
	}

	public function testItHandlesUploadedMediaWithGivenValues() {
		$fileInfo = new MediaFile(
			id: $this->randomId(),
			handler: 'default',
			details: ['key' => 'value'],
		);

		$command = new HandleUploadedMedia(
			file: $this->createStub(UploadedFileInterface::class),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'A strange encounter',
			accessibilityText: 'Nicolas Cage sitting next to Andy Samberg as Nicolas Cage',
			attribution: 'Courtesy Broadway Video/NBC',
		);

		$handler = $this->createMock(MediaHandler::class);
		$handler->expects($this->once())->method('handleUploadedFile')->with(
			$this->equalTo($command->file),
			$this->equalTo($command->userId),
			$this->equalTo($command->siteId),
		)->willReturn($fileInfo);

		$registry = $this->createStub(MediaHandlerRegistry::class);
		$registry->method('get')->willReturn($handler);

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(MediaAdded::class));

		$service = new MediaService($bus, $registry);
		$service->onHandleUploadedMedia($command);


		$media = new Media(
			id: $command->createdMedia?->id,
			userId: $command->userId,
			siteId: $command->siteId,
			title: 'A strange encounter',
			accessibilityText: 'Nicolas Cage sitting next to Andy Samberg as Nicolas Cage',
			type: MediaType::Image,
			handler: 'default',
			attribution: 'Courtesy Broadway Video/NBC',
			info: ['key' => 'value'],
		);

		$this->assertEquals($media, $command->createdMedia);
	}
}
