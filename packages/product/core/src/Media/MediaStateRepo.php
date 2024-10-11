<?php

namespace Smolblog\Core\Media;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Media\Events\MediaCreated;
use Smolblog\Core\Media\Events\MediaAttributesUpdated;
use Smolblog\Core\Media\Events\MediaDeleted;
use Smolblog\Core\Media\Queries\MediaById;
use Smolblog\Core\Media\Queries\UserCanEditMedia;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Service\Messaging\ExecutionListener;
use Smolblog\Foundation\Service\Messaging\Projection;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Store media objects in a simple key-value store.
 */
class MediaStateRepo implements Projection {
	public const TABLE = 'media_states';

	/**
	 * Construct the projection.
	 *
	 * @param ConnectionInterface $db  Working database connection.
	 * @param MessageBus          $bus Active MessageBus.
	 */
	public function __construct(
		private ConnectionInterface $db,
		private MessageBus $bus,
	) {
	}

	public function mediaExists(Identifier $id): bool {
		return $this->db->table(self::TABLE)->where('media_uuid', $id->toString())->exists();
	}

	/**
	 * Get a single media object.
	 *
	 * @param Identifier $id ID of the media to get.
	 * @return Media|null
	 */
	public function getSingleMedia(Identifier $id): ?Media {
		$row = $this->db->table(self::TABLE)->where('media_uuid', $id->toString())->value('media');

		return isset($row) ? Media::fromJson($row) : null;
	}

	/**
	 * Get many media objects.
	 *
	 * @param Identifier[] $ids Array of media IDs to get.
	 * @return Media[]
	 */
	public function getMultipleMedia(array $ids): array {
		$rows = $this->db->table(self::TABLE)->
			whereIn('media_uuid', array_map(fn($id) => $id->toString(), $ids))->
			get();

		return array_map(fn($row) => Media::fromJson($row->media), $rows->all());
	}

	#[ExecutionListener]
	public function onMediaCreated(MediaCreated $event): void {
		$this->db->table(self::TABLE)->insert([
			'media_uuid' => $event->media->id->toString(),
			'site_uuid' => $event->media->siteId->toString(),
			'user_uuid' => $event->media->authorId->toString(),
			'media' => json_encode($event->media),
		]);
	}

	#[ExecutionListener]
	public function onMediaAttributesUpdated(MediaAttributesUpdated $event): void {
		$current = $this->getSingleMedia($event->entityId);
		$new = $current->with(
			title: $event->title ?? $current->title,
			accessibilityText: $event->accessibilityText ?? $current->accessibilityText,
		);

		$this->db->table(self::TABLE)->
			where('media_uuid', '=', strval($event->entityId))->
			update(['media' => json_encode($new)]);
	}

	#[ExecutionListener]
	public function onMediaDeleted(MediaDeleted $event): void {
		$this->db->table(self::TABLE)->where('media_uuid', $event->entityId->toString())->delete();
	}

	#[ExecutionListener]
	public function onMediaById(MediaById $query): void {
		$query->setResults($this->getSingleMedia($query->id));
	}

	#[ExecutionListener]
	public function onUserCanEditMedia(UserCanEditMedia $query): void {
		$query->setResults(
			$this->checkUserAndMedia(mediaId: $query->mediaId, userId: $query->userId, needsEdit: true)
		);
	}

	private function checkUserAndMedia(
		Identifier $mediaId,
		?Identifier $userId = null,
		bool $needsEdit = false
	): bool {
		$media = $this->getSingleMedia($mediaId);
		if (!isset($media)) {
			return false;
		}
		if (!$needsEdit || $media->userId == $userId) {
			return true;
		}

		return isset($userId) && $this->bus->fetch(new UserHasPermissionForSite(
			siteId: $media->siteId,
			userId: $userId,
			mustBeAdmin: true,
		));
	}
}
