<?php

namespace Smolblog\Core\Content\Media;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Test\Kits\EventComparisonTestKit;
use Smolblog\Test\Kits\MessageBusMockKit;
use Smolblog\Test\TestCase;

final class MediaServiceTest extends TestCase {
	use EventComparisonTestKit;
	use MessageBusMockKit;

	public function testItCreatesTypesFromMimeTypes() {
		$this->assertEquals(MediaType::File, MediaService::typeFromMimeType('text'));
		$this->assertEquals(MediaType::File, MediaService::typeFromMimeType('application/pdf'));
		$this->assertEquals(MediaType::Image, MediaService::typeFromMimeType('image/jpeg'));
		$this->assertEquals(MediaType::Audio, MediaService::typeFromMimeType('audio/mp3'));
		$this->assertEquals(MediaType::Video, MediaService::typeFromMimeType('video/quicktime'));
	}

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
		$this->messageBusShouldDispatch($bus,
			$this->eventEquivalentTo(new MediaFileAdded(
				contentId: $fileInfo->id,
				userId: $command->userId,
				siteId: $command->siteId,
				handler: $fileInfo->handler,
				mimeType: $fileInfo->mimeType,
				details: $fileInfo->details,
			)),
			$this->eventEquivalentTo(new MediaAdded(
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
				title: 'IMG_90108.jpg',
				accessibilityText: 'A thing.',
				type: MediaType::Image,
				thumbnailUrl: '//img/thumb.jpg',
				defaultUrl: '//img/orig.png',
				file: $fileInfo,
			)),
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
		$this->messageBusShouldDispatch($bus,
			$this->eventEquivalentTo(new MediaFileAdded(
				contentId: $fileInfo->id,
				userId: $command->userId,
				siteId: $command->siteId,
				handler: $fileInfo->handler,
				mimeType: $fileInfo->mimeType,
				details: $fileInfo->details,
			)),
			$this->eventEquivalentTo(new MediaAdded(
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
				title: 'A strange encounter',
				accessibilityText: 'Nicolas Cage sitting next to Andy Samberg as Nicolas Cage',
				type: MediaType::Image,
				thumbnailUrl: '//img/thumb.jpg',
				defaultUrl: '//img/orig.png',
				file: $fileInfo,
			)),
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
		$this->messageBusShouldDispatch($bus,
			$this->eventEquivalentTo(new MediaFileAdded(
				contentId: $fileInfo->id,
				userId: $command->userId,
				siteId: $command->siteId,
				handler: $fileInfo->handler,
				mimeType: $fileInfo->mimeType,
				details: $fileInfo->details,
			)),
			$this->eventEquivalentTo(new MediaAdded(
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
				title: 'A strange encounter',
				accessibilityText: 'Nicolas Cage sitting next to Andy Samberg as Nicolas Cage',
				type: MediaType::Image,
				thumbnailUrl: '//img/thumb.jpg',
				defaultUrl: '//img/orig.png',
				file: $fileInfo,
			)),
		);

		$service = new MediaService($bus, $registry);
		$service->onSideloadMedia($command);
	}

	public function testItHandlesUpdatingMediaAttributes() {
		$params = [
			'contentId' => $this->randomId(),
			'userId' => $this->randomId(),
			'siteId' => $this->randomId(),
			'title' => 'Something',
			'accessibilityText' => 'A screenshot of something.com that says something.',
		];

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo(
			new MediaAttributesEdited(...$params)
		));

		$service = new MediaService(bus: $bus, registry: $this->createStub(MediaHandlerRegistry::class));
		$service->onEditMediaAttributes(
			new EditMediaAttributes(...$params)
		);
	}

	public function testItHandlesDeletingMedia() {
		$params = [
			'contentId' => $this->randomId(),
			'userId' => $this->randomId(),
			'siteId' => $this->randomId(),
		];

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo(
			new MediaDeleted(...$params)
		));

		$service = new MediaService(bus: $bus, registry: $this->createStub(MediaHandlerRegistry::class));
		$service->onDeleteMedia(
			new DeleteMedia(...$params)
		);
	}

	public function testItCreatesHtmlForMedia() {
		$service = new MediaService(
			bus: $this->createStub(MessageBus::class),
			registry: $this->createStub(MediaHandlerRegistry::class),
		);

		$standardProps = [
			'id' => $this->randomId(),
			'userId' => $this->randomId(),
			'siteId' => $this->randomId(),
			'title' => 'Thing',
			'accessibilityText' => 'Description of the thing.',
			'thumbnailUrl' => '//cdn.smol.blog/thumb.png',
			'defaultUrl' => '//cdn.smol.blog/thing.png',
			'file' => $this->createStub(MediaFile::class),
		];

		$this->assertEquals(
			"<img src='//cdn.smol.blog/thing.png' alt='Description of the thing.'>",
			$service->htmlForMedia(new Media(...$standardProps, type: MediaType::Image)),
		);
		$this->assertEquals(
			"<video src='//cdn.smol.blog/thing.png' alt='Description of the thing.'></video>",
			$service->htmlForMedia(new Media(...$standardProps, type: MediaType::Video)),
		);
		$this->assertEquals(
			"<audio src='//cdn.smol.blog/thing.png' alt='Description of the thing.'></audio>",
			$service->htmlForMedia(new Media(...$standardProps, type: MediaType::Audio)),
		);
		$this->assertEquals(
			"<a href='//cdn.smol.blog/thing.png'>Thing</a>",
			$service->htmlForMedia(new Media(...$standardProps, type: MediaType::File)),
		);
	}

	public function testItAddsMediaHtmlToMessages() {
		$service = new MediaService(
			bus: $this->createStub(MessageBus::class),
			registry: $this->createStub(MediaHandlerRegistry::class),
		);

		$message = $this->createMock(NeedsMediaRendered::class);
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
