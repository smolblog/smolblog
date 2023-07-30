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
		$file->method('getClientMediaType')->willReturn('image/png');

		$fileInfo = new MediaFile(
			id: $this->randomId(),
			handler: 'default',
			details: ['key' => 'value'],
		);

		$command = new HandleUploadedMedia(
			file: $file,
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'A thing.'
		);

		$handler = $this->createMock(MediaHandler::class);
		$handler->expects($this->once())->method('handleUploadedFile')->with(
			$this->equalTo($command->file),
			$this->equalTo($command->userId),
			$this->equalTo($command->siteId),
		)->willReturn($fileInfo);
		$handler->method('getThumbnailUrlFor')->willReturn('//img/thumb.jpg');
		$handler->method('getUrlFor')->willReturn('//img/orig.png');
		$handler->method('getHtmlFor')->willReturn('<img src="//img/orig.png">');

		$registry = $this->createStub(MediaHandlerRegistry::class);
		$registry->method('get')->willReturn($handler);

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new MediaFileAdded(
				contentId: $fileInfo->id,
				userId: $command->userId,
				siteId: $command->siteId,
				handler: $fileInfo->handler,
				mimeType: $fileInfo->mimeType,
				details: $fileInfo->details,
			))],
			[$this->eventEquivalentTo(new MediaAdded(
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
				title: 'IMG_90108.jpg',
				accessibilityText: 'A thing.',
				type: MediaType::Image,
				thumbnailUrl: '//img/thumb.jpg',
				defaultUrl: '//img/orig.png',
				defaultHtml: '<img src="//img/orig.png">',
				file: $fileInfo,
			))],
		);

		$service = new MediaService($bus, $registry);
		$service->onHandleUploadedMedia($command);
	}

	public function testItHandlesUploadedMediaWithGivenValues() {
		$fileInfo = new MediaFile(
			id: $this->randomId(),
			handler: 'default',
			mimeType: 'image/gif',
			details: ['key' => 'value'],
		);

		$command = new HandleUploadedMedia(
			file: $this->createStub(UploadedFileInterface::class),
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'A strange encounter',
			accessibilityText: 'Nicolas Cage sitting next to Andy Samberg as Nicolas Cage',
		);

		$handler = $this->createMock(MediaHandler::class);
		$handler->expects($this->once())->method('handleUploadedFile')->with(
			$this->equalTo($command->file),
			$this->equalTo($command->userId),
			$this->equalTo($command->siteId),
		)->willReturn($fileInfo);
		$handler->method('getThumbnailUrlFor')->willReturn('//img/thumb.jpg');
		$handler->method('getUrlFor')->willReturn('//img/orig.png');
		$handler->method('getHtmlFor')->willReturn('<img src="//img/orig.png">');

		$registry = $this->createStub(MediaHandlerRegistry::class);
		$registry->method('get')->willReturn($handler);

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new MediaFileAdded(
				contentId: $fileInfo->id,
				userId: $command->userId,
				siteId: $command->siteId,
				handler: $fileInfo->handler,
				mimeType: $fileInfo->mimeType,
				details: $fileInfo->details,
			))],
			[$this->eventEquivalentTo(new MediaAdded(
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
				title: 'A strange encounter',
				accessibilityText: 'Nicolas Cage sitting next to Andy Samberg as Nicolas Cage',
				type: MediaType::Image,
				thumbnailUrl: '//img/thumb.jpg',
				defaultUrl: '//img/orig.png',
				defaultHtml: '<img src="//img/orig.png">',
				file: $fileInfo,
			))],
		);

		$service = new MediaService($bus, $registry);
		$service->onHandleUploadedMedia($command);
	}
}
