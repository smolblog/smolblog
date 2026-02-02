<?php

namespace Smolblog\Core\Test;

use Cavatappi\Test\ModelTest;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Content\Commands\{CreateContent, DeleteContent, UpdateContent};
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Entities\{Content, ContentExtension, ContentType, ContentTypeConfiguration};
use Smolblog\Core\Content\Events\{ContentCreated, ContentDeleted, ContentUpdated};
use Smolblog\Core\Content\Services\{ContentExtensionRegistry, ContentTypeService, DefaultContentTypeService};
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Test\Setup\ContentExtensionTestContentType;
use Smolblog\Core\Test\Setup\ContentExtensionTestContentTypeService;

abstract class ContentExtensionTest extends ModelTest {
	public const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	public const string EXTENSION_KEY = 'invalid';
	public const string SERVICE_CLASS = self::class;
	public const string EXTENSION_CLASS = self::class;

	protected ContentRepo&MockObject $contentRepo;
	protected SitePermissionsService&MockObject $perms;

	protected function createMockServices(): array {
		$this->contentRepo = $this->createMock(ContentRepo::class);
		$this->perms = $this->createMock(SitePermissionsService::class);

		return [
			ContentRepo::class => fn() => $this->contentRepo,
			SitePermissionsService::class => fn() => $this->perms,
			ContentExtensionTestContentTypeService::class => ['eventBus' => EventDispatcherInterface::class],
			...parent::createMockServices(),
		];
	}

	protected function setUp(): void {
		if (static::EXTENSION_KEY == 'invalid' || static::SERVICE_CLASS == self::class || static::EXTENSION_CLASS == self::class) {
			throw new \Exception(message: 'Test constants are not correctly set in ' . static::class);
		}

		parent::setUp();
	}

	abstract protected function createExampleExtension(): ContentExtension;
	abstract protected function createModifiedExtension(): ContentExtension;

	protected function createExampleContentBody(): ContentType {
		return new ContentExtensionTestContentType(title: 'Hello hello');
	}

	public function testItIsCorrectlyRegistered() {
		$reg = $this->app->container->get(ContentExtensionRegistry::class);

		$this->assertTrue($reg->has(static::EXTENSION_KEY));
		$this->assertInstanceOf(static::SERVICE_CLASS, $reg->getService(static::EXTENSION_KEY));
		$this->assertArrayHasKey(static::EXTENSION_KEY, $reg->availableContentExtensions());
		$this->assertEquals(static::EXTENSION_CLASS, $reg->extensionClassFor(static::EXTENSION_KEY));
	}

	public function testItCanBeCreated() {
		$contentId = $this->randomId();
		$command = new CreateContent(
			userId: $this->randomId(),
			body: $this->createExampleContentBody(),
			siteId: $this->randomId(),
			contentId: $contentId,
			extensions: [$this->createExampleExtension()],
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->perms->method('canCreateContent')->willReturn(true);

		$this->expectEvent(new ContentCreated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			extensions: $command->extensions,
		));

		$this->app->execute($command);
	}

	public function testItCanBeUpdated() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: $this->createExampleContentBody(),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $userId,
			extensions: [$this->createModifiedExtension()],
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $this->createExampleContentBody(),
			siteId: $command->siteId,
			userId: $userId,
			id: $contentId,
			extensions: [$this->createExampleExtension()],
		));

		$this->expectEvent(new ContentUpdated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $userId,
			entityId: $contentId,
			contentUserId: $userId,
			extensions: $command->extensions,
		));

		$this->app->execute($command);
	}

	public function testItCanBeDeleted() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new DeleteContent(
			userId: $userId,
			contentId: $contentId,
		);
		$content = new Content(
			body: $this->createExampleContentBody(),
			siteId: $this->randomId(),
			userId: $userId,
			id: $contentId,
			extensions: [$this->createExampleExtension()],
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn($content);

		$this->expectEvent(new ContentDeleted(
			aggregateId: $content->siteId,
			userId: $userId,
			entityId: $contentId,
		));

		$this->app->execute($command);
	}
}
