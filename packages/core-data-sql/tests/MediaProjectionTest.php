<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Infrastructure\Serialization\SerializationService;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\{MediaCreated, MediaAttributesUpdated, MediaDeleted};
use Smolblog\CoreDataSql\Test\DataTestBase;
use stdClass;

#[AllowMockObjectsWithoutExpectations]
final class MediaProjectionTest extends DataTestBase {
	public function testMediaCreated() {
		$projection = $this->app->container->get(MediaProjection::class);

		$media = new Media(
			id: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'hey',
			accessibilityText: 'what is going on',
			type: MediaType::Image,
			handler: 'test',
			fileDetails: ['one' => 2],
		);
		$event = MediaCreated::createFromMediaObject($media);

		$this->assertFalse($projection->hasMediaWithId($media->id));
		$this->assertNull($projection->mediaById($media->id));
		$this->app->dispatch($event);
		$this->assertTrue($projection->hasMediaWithId($media->id));
		$this->assertValueObjectEquals($media, $projection->mediaById($media->id));
	}

	public function testMediaUpdated() {
		$projection = $this->app->container->get(MediaProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$oldMedia = new Media(
			id: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'hey',
			accessibilityText: 'what is going on',
			type: MediaType::Image,
			handler: 'test',
			fileDetails: ['one' => 2],
		);
		$newMedia = $oldMedia->with(
			title: 'i said hey',
		);
		$event = new MediaAttributesUpdated(
			aggregateId: $oldMedia->siteId,
			userId: $oldMedia->userId,
			entityId: $oldMedia->id,
			title: 'i said hey',
		);

		$db->insert($env->tableName('media'), [
			'media_uuid' => $oldMedia->id,
			'site_uuid' => $oldMedia->siteId,
			'media_obj' => json_encode($oldMedia),
		]);
		$this->assertValueObjectEquals($oldMedia, $projection->mediaById($oldMedia->id));

		$this->app->dispatch($event);
		$this->assertValueObjectEquals($newMedia, $projection->mediaById($oldMedia->id));
	}

	public function testMediaDeleted() {
		$projection = $this->app->container->get(MediaProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$media = new Media(
			id: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'hey',
			accessibilityText: 'what is going on',
			type: MediaType::Image,
			handler: 'test',
			fileDetails: ['one' => 2],
		);
		$db->insert($env->tableName('media'), [
			'media_uuid' => $media->id,
			'site_uuid' => $media->siteId,
			'media_obj' => json_encode($media),
		]);
		$this->assertTrue($projection->hasMediaWithId($media->id));

		$event = new MediaDeleted(
			userId: $media->userId,
			aggregateId: $media->siteId,
			entityId: $media->id,
		);
		$this->app->dispatch($event);
		$this->assertFalse($projection->hasMediaWithId($media->id));
	}

	public function testItFailsSilentlyOnEditIfMediaDoesNotExist() {
		$projection = $this->app->container->get(MediaProjection::class);

		$missingMedia = new Media(
			id: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'hey',
			accessibilityText: 'what is going on',
			type: MediaType::Image,
			handler: 'test',
			fileDetails: ['one' => 2],
		);
		$this->assertFalse($projection->hasMediaWithId($missingMedia->id));

		$projection->onMediaAttributesUpdated(
			new MediaAttributesUpdated(
				title: 'another one',
				aggregateId: $missingMedia->siteId,
				userId: $missingMedia->userId,
				entityId: $missingMedia->id,
			),
		);
		$this->assertFalse($projection->hasMediaWithId($missingMedia->id));
	}
}
