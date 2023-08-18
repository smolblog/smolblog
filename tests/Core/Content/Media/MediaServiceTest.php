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
				file: $fileInfo,
			))],
		);

		$service = new MediaService($bus, $registry);
		$service->onHandleUploadedMedia($command);
	}

	public function testItHandlesSideloadedMediaWithGivenValues() {
		$fileInfo = new MediaFile(
			id: $this->randomId(),
			handler: 'default',
			mimeType: 'image/gif',
			details: ['key' => 'value'],
		);

		$command = new SideloadMedia(
			url: '//cdn.bookface.social/uploads/thing.gif',
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'A strange encounter',
			accessibilityText: 'Nicolas Cage sitting next to Andy Samberg as Nicolas Cage',
		);

		$handler = $this->createMock(MediaHandler::class);
		$handler->expects($this->once())->method('sideloadFile')->with(
			$this->equalTo($command->url),
			$this->equalTo($command->userId),
			$this->equalTo($command->siteId),
		)->willReturn($fileInfo);
		$handler->method('getThumbnailUrlFor')->willReturn('//img/thumb.jpg');
		$handler->method('getUrlFor')->willReturn('//img/orig.png');

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
				file: $fileInfo,
			))],
		);

		$service = new MediaService($bus, $registry);
		$service->onSideloadMedia($command);
	}

	public function testItCreatesHtmlForMedia() {
		$service = new MediaService(
			bus: $this->createStub(MessageBus::class),
			registry: $this->createStub(MediaHandlerRegistry::class),
		);

		$image = new Media(
			id: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'Image',
			accessibilityText: 'Description of the thing.',
			type: MediaType::Image,
			thumbnailUrl: '//cdn.smol.blog/thumb.png',
			defaultUrl: '//cdn.smol.blog/thing.png',
			file: $this->createStub(MediaFile::class),
		);
		$this->assertEquals(
			"<img src='//cdn.smol.blog/thing.png' alt='Description of the thing.'>",
			$service->htmlForMedia($image),
		);
	}

	public function testItAddsMediaHtmlToMessages() {
		$service = new MediaService(
			bus: $this->createStub(MessageBus::class),
			registry: $this->createStub(MediaHandlerRegistry::class),
		);

		$message = $this->createStub(NeedsMediaRendered::class);
		$message->method('getMediaObjects')->willReturn([
			new Media(
				id: $this->randomId(),
				userId: $this->randomId(),
				siteId: $this->randomId(),
				title: 'Image',
				accessibilityText: 'Description of the thing.',
				type: MediaType::Image,
				thumbnailUrl: '//cdn.smol.blog/thumb.png',
				defaultUrl: '//cdn.smol.blog/thing.png',
				file: $this->createStub(MediaFile::class),
			),
			new Media(
				id: $this->randomId(),
				userId: $this->randomId(),
				siteId: $this->randomId(),
				title: 'Image',
				accessibilityText: 'Words describing that thing.',
				type: MediaType::Image,
				thumbnailUrl: '//cdn.smol.blog/thumb.png',
				defaultUrl: '//cdn.smol.blog/that.png',
				file: $this->createStub(MediaFile::class),
			)
		]);
		$message->expects($this->once())->method('setMediaHtml')->with([
			"<img src='//cdn.smol.blog/thing.png' alt='Description of the thing.'>",
			"<img src='//cdn.smol.blog/that.png' alt='Words describing that thing.'>"
		]);

		$service->onNeedsMediaRendered($message);
	}
}
