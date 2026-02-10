<?php

namespace Smolblog\Core\Test;

use Cavatappi\Test\ModelTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Media\Commands\{CreateMedia, DeleteMedia, EditMediaAttributes, HandleUploadedMedia, SideloadMedia, UpdateMedia};
use Smolblog\Core\Media\Data\MediaRepo;
use Smolblog\Core\Media\Entities\{Media, MediaExtension, MediaType};
use Smolblog\Core\Media\Events\{MediaAttributesUpdated, MediaCreated, MediaDeleted, MediaUpdated};
use Smolblog\Core\Media\Services\{MediaExtensionRegistry};
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Test\Setup\MediaExtensionTestMediaHandler;
use Smolblog\Core\Test\Stubs\TestUploadedFileInterface;

abstract class MediaExtensionTest extends ModelTest {
	public const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	public const string EXTENSION_KEY = 'invalid';
	public const string SERVICE_CLASS = self::class;
	public const string EXTENSION_CLASS = self::class;

	protected MediaRepo&MockObject $mediaRepo;
	protected MediaExtensionTestMediaHandler&Stub  $handler;
	protected SitePermissionsService&MockObject $perms;

	protected function createMockServices(): array {
		$this->mediaRepo = $this->createMock(MediaRepo::class);
		$this->handler = $this->createStub(MediaExtensionTestMediaHandler::class);
		$this->perms = $this->createMock(SitePermissionsService::class);

		return [
			MediaRepo::class => fn() => $this->mediaRepo,
			MediaExtensionTestMediaHandler::class => fn() => $this->handler,
			SitePermissionsService::class => fn() => $this->perms,
			...parent::createMockServices(),
		];
	}

	protected function setUp(): void {
		if (static::EXTENSION_KEY == 'invalid' || static::SERVICE_CLASS == self::class || static::EXTENSION_CLASS == self::class) {
			throw new \Exception(message: 'Test constants are not correctly set in ' . static::class);
		}

		parent::setUp();
	}

	abstract protected function createExampleExtension(): MediaExtension;
	abstract protected function createModifiedExtension(): MediaExtension;

	public function testItIsCorrectlyRegistered() {
		$reg = $this->app->container->get(MediaExtensionRegistry::class);

		$this->assertTrue($reg->has(static::EXTENSION_KEY));
		$this->assertInstanceOf(static::SERVICE_CLASS, $reg->getService(static::EXTENSION_KEY));
		$this->assertArrayHasKey(static::EXTENSION_KEY, $reg->availableMediaExtensions());
		$this->assertEquals(static::EXTENSION_CLASS, $reg->findClass(static::EXTENSION_KEY));

		$this->assertFalse(
			property_exists(static::EXTENSION_CLASS, 'type'),
			'Extension class cannot have property \'type\' as it conflicts with (de)serialization.',
		);
	}

	public function testItCanBeCreatedByUpload() {
		$mediaId = $this->randomId();
		$title = 'IMG_0543.jpg';
		$uploadedFile = new TestUploadedFileInterface();
		$command = new HandleUploadedMedia(
			file: $uploadedFile,
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'Alt Text',
			extensions: [$this->createExampleExtension()],
			mediaId: $mediaId,
			title: $title,
		);
		$createdMedia = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: $title,
			accessibilityText: $command->accessibilityText,
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: ['one' => 'two'],
			extensions: $command->extensions,
		);

		$this->mediaRepo->method('hasMediaWithId')->willReturn(false);
		$this->perms->method('canUploadMedia')->willReturn(true);
		$this->handler->method('handleUploadedFile')->willReturn($createdMedia);

		$this->expectEvent(MediaCreated::createFromMediaObject($createdMedia));

		$this->app->execute($command);
	}

	public function testItCanBeCreatedBySideload() {
		$mediaId = $this->randomId();
		$title = 'IMG_0543.jpg';
		$command = new SideloadMedia(
			url: 'https://smol.blog/img.jpg',
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'Alt Text',
			extensions: [$this->createExampleExtension()],
			mediaId: $mediaId,
			title: $title,
		);
		$createdMedia = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: $title,
			accessibilityText: $command->accessibilityText,
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: ['one' => 'two'],
			extensions: $command->extensions,
		);

		$this->mediaRepo->method('hasMediaWithId')->willReturn(false);
		$this->perms->method('canUploadMedia')->willReturn(true);
		$this->handler->method('sideloadFile')->willReturn($createdMedia);

		$this->expectEvent(MediaCreated::createFromMediaObject($createdMedia));

		$this->app->execute($command);
	}

	public function testItCanBeUpdated() {
		$mediaId = $this->randomId();
		$userId = $this->randomId();
		$siteId = $this->randomId();
		$newExt = [$this->createModifiedExtension()];
		$command = new EditMediaAttributes(
			mediaId: $mediaId,
			userId: $userId,
			extensions: $newExt,
		);

		$this->mediaRepo->method('hasMediaWithId')->willReturn(true);
		$this->mediaRepo->method('mediaById')->willReturn(new Media(
			id: $mediaId,
			userId: $userId,
			siteId: $siteId,
			title: 'No change',
			accessibilityText: 'No change',
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: ['one' => 'two'],
			extensions: [$this->createExampleExtension()],
		));

		$this->expectEvent(new MediaAttributesUpdated(
			entityId: $mediaId,
			userId: $userId,
			aggregateId: $siteId,
			extensions: $command->extensions,
		));

		$this->app->execute($command);
	}

	public function testItCanBeDeleted() {
		$mediaId = $this->randomId();
		$userId = $this->randomId();
		$command = new DeleteMedia(
			userId: $userId,
			mediaId: $mediaId,
		);
		$media = new Media(
			siteId: $this->randomId(),
			userId: $userId,
			id: $mediaId,
			title: 'No change',
			accessibilityText: 'No change',
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: ['one' => 'two'],
			extensions: [$this->createExampleExtension()],
		);

		$this->mediaRepo->method('hasMediaWithId')->willReturn(true);
		$this->mediaRepo->method('mediaById')->willReturn($media);

		$this->expectEvent(new MediaDeleted(
			aggregateId: $media->siteId,
			userId: $userId,
			entityId: $mediaId,
		));

		$this->app->execute($command);
	}
}
