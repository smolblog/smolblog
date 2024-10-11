<?php

namespace Smolblog\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Core\Content\Commands\{CreateContent, DeleteContent, UpdateContent};
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Content\Events\{ContentCreated, ContentDeleted, ContentUpdated};
use Smolblog\Core\Content\Services\ContentTypeRegistry;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Site\Entities\UserSitePermissions;

abstract class ContentTypeTest extends ModelTest {
	const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	const string TYPE_KEY = 'invalid';
	const string SERVICE_CLASS = self::class;
	const string TYPE_CLASS = self::class;

	protected const CREATE_EVENT = ContentCreated::class;
	protected const UPDATE_EVENT = ContentUpdated::class;
	protected const DELETE_EVENT = ContentDeleted::class;

	protected ContentRepo & MockObject $contentRepo;
	protected SitePermissionsService & MockObject $perms;

	protected function createMockServices(): array {
		$this->contentRepo = $this->createMock(ContentRepo::class);
		$this->perms = $this->createMock(SitePermissionsService::class);

		return [
			ContentRepo::class => fn() => $this->contentRepo,
			SitePermissionsService::class => fn() => $this->perms,
		];
	}

	protected function setUp(): void {
		if (static::TYPE_KEY == 'invalid' || static::SERVICE_CLASS == self::class || static::TYPE_CLASS == self::class) {
			throw new \Exception(message: 'Test constants are not correctly set in ' . static::class);
		}

		parent::setUp();
	}

	abstract protected function createExampleType(): ContentType;
	abstract protected function createModifiedType(): ContentType;

	public function testItIsCorrectlyRegistered() {
		$reg = $this->app->container->get(ContentTypeRegistry::class);

		$this->assertTrue($reg->has(static::TYPE_KEY));
		$this->assertInstanceOf(static::SERVICE_CLASS, $reg->getService(static::TYPE_KEY));
		$this->assertArrayHasKey(static::TYPE_KEY, $reg->availableContentTypes());
		$this->assertEquals(static::TYPE_CLASS, $reg->typeClassFor(static::TYPE_KEY));
	}

	public function testItCanBeCreated() {
		$contentId = $this->randomId();
		$command = new CreateContent(
			userId: $this->randomId(),
			body: $this->createExampleType(),
			siteId: $this->randomId(),
			contentId: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(false);
		$this->perms->method('canCreateContent')->willReturn(true);

		$this->expectEvent(new (static::CREATE_EVENT)(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
		));

		$this->app->execute($command);
	}

	public function testItCanBeUpdated() {
		$contentId = $this->randomId();
		$userId = $this->randomId();
		$command = new UpdateContent(
			body: $this->createModifiedType(),
			siteId: $this->randomId(),
			userId: $userId,
			contentId: $contentId,
			contentUserId: $userId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn(new Content(
			body: $this->createExampleType(),
			siteId: $command->siteId,
			userId: $userId,
			id: $contentId,
		));

		$this->expectEvent(new (static::UPDATE_EVENT)(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $userId,
			entityId: $contentId,
			contentUserId: $userId,
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
			body: $this->createExampleType(),
			siteId: $this->randomId(),
			userId: $userId,
			id: $contentId,
		);

		$this->contentRepo->method('hasContentWithId')->willReturn(true);
		$this->contentRepo->method('contentById')->willReturn($content);

		$this->expectEvent(new (static::DELETE_EVENT)(
			aggregateId: $content->siteId,
			userId: $userId,
			entityId: $contentId,
		));

		$this->app->execute($command);
	}
}
