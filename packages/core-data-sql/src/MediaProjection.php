<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\DomainEvent\EventListenerService;
use Cavatappi\Foundation\DomainEvent\ProjectionListener;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use Doctrine\DBAL\Schema\Schema;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Media\Data\MediaRepo;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Events\{MediaAttributesUpdated, MediaCreated, MediaDeleted};

/**
 * Save state for media objects.
 */
class MediaProjection implements DatabaseTableHandler, EventListenerService, MediaRepo {
	/**
	 * Create the content table.
	 *
	 * @param Schema   $schema    Schema to add the content table to.
	 * @param callable $tableName Function to create a prefixed table name from a given table name.
	 * @return Schema
	 */
	public static function addTableToSchema(Schema $schema, callable $tableName): Schema {
		$table = $schema->createTable($tableName('media'));
		$table->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$table->addColumn('media_uuid', 'guid');
		$table->addColumn('site_uuid', 'guid');
		$table->addColumn('media_obj', 'json');

		$table->setPrimaryKey(['dbid']);
		$table->addUniqueIndex(['media_uuid']);
		$table->addIndex(['site_uuid']);

		return $schema;
	}

	/**
	 * Create the service.
	 *
	 * @param DatabaseService      $db    Working database connection.
	 * @param SerializationService $serde Configured (de)serialization service.
	 */
	public function __construct(
		private DatabaseService $db,
		private SerializationService $serde,
	) {}

	/**
	 * Check if a given media object exists.
	 *
	 * @param UuidInterface $mediaId ID to check.
	 * @return boolean
	 */
	public function hasMediaWithId(UuidInterface $mediaId): bool {
		$query = $this->db->createUnprefixedQueryBuilder();
		$query
			->select('1')
			->from($this->db->tableName('media'))
			->where('media_uuid = ?')
			->setParameter(0, $mediaId);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Get the specified Media object.
	 *
	 * @param UuidInterface $mediaId Media to fetch.
	 * @return Media|null
	 */
	public function mediaById(UuidInterface $mediaId): ?Media {
		$query = $this->db->createUnprefixedQueryBuilder();
		$query
			->select('media_obj')
			->from($this->db->tableName('media'))
			->where('media_uuid = ?')
			->setParameter(0, $mediaId);
		$result = $query->fetchOne();

		if ($result === false) {
			return null;
		}

		// This has to do with different DB engines which we cannot currently test.
		return is_string($result)
			? $this->serde->fromJson($result, Media::class)
			: $this->serde->fromArray($result, Media::class); // @codeCoverageIgnore
	}

	/**
	 * Save a new media object.
	 *
	 * @param MediaCreated $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onMediaCreated(MediaCreated $event): void {
		$media = $event->getMediaObject();

		$this->db->insert('media', [
			'media_uuid' => $media->id,
			'site_uuid' => $media->siteId,
			'media_obj' => $this->serde->toJson($media),
		]);
	}

	/**
	 * Update attributes for a media object.
	 *
	 * @param MediaAttributesUpdated $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onMediaAttributesUpdated(MediaAttributesUpdated $event): void {
		$existing = $this->mediaById($event->entityId ?? UuidFactory::nil());
		if (!isset($existing)) {
			return;
		}

		$updated = $existing->with(
			title: $event->title ?? $existing->title,
			accessibilityText: $event->accessibilityText ?? $existing->accessibilityText,
		);
		$this->db->update(
			'media',
			['media_obj' => $this->serde->toJson($updated)],
			['media_uuid' => $updated->id],
		);
	}

	/**
	 * Remove a media object.
	 *
	 * @param MediaDeleted $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onMediaDeleted(MediaDeleted $event): void {
		$this->db->delete('media', ['media_uuid' => $event->entityId]);
	}
}
