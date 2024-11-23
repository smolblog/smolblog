<?php

namespace Smolblog\CoreDataSql;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\MediaChannelEntry;
use Smolblog\Core\Channel\Events\MediaPushedToChannel;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\{MediaCreated, MediaAttributesUpdated, MediaDeleted};
use Smolblog\CoreDataSql\Test\DataTestBase;
use Smolblog\Foundation\Value\Fields\Url;
use stdClass;

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
		$this->assertObjectEquals($media, $projection->mediaById($media->id) ?? new stdClass());
	}

	public function testMediaUpdated() {
		$projection = $this->app->container->get(MediaProjection::class);
		$db = $this->app->container->get(DatabaseManager::class)->getConnection();

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
			title: 'i said hey'
		);
		$event = new MediaAttributesUpdated(
			aggregateId: $oldMedia->siteId,
			userId: $oldMedia->userId,
			entityId: $oldMedia->id,
			title: 'i said hey',
		);

		$db->insert('media', [
			'media_uuid' => $oldMedia->id,
			'site_uuid' => $oldMedia->siteId,
			'media_obj' => json_encode($oldMedia),
		]);
		$this->assertObjectEquals($oldMedia, $projection->mediaById($oldMedia->id) ?? new stdClass());

		$this->app->dispatch($event);
		$this->assertObjectEquals($newMedia, $projection->mediaById($oldMedia->id) ?? new stdClass());
	}

	public function testMediaDeleted() {
		$projection = $this->app->container->get(MediaProjection::class);
		$db = $this->app->container->get(DatabaseManager::class)->getConnection();

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
		$db->insert('media', [
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
			)
		);
		$this->assertFalse($projection->hasMediaWithId($missingMedia->id));
	}
}
