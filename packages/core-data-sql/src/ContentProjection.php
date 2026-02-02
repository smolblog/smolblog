<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\DomainEvent\ProjectionListener;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use Doctrine\DBAL\Schema\Schema;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Data\ContentStateManager;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\{ContentCanonicalUrlSet, ContentCreated, ContentDeleted, ContentUpdated};

/**
 * Store and retrieve Content objects.
 */
class ContentProjection implements ContentRepo, ContentStateManager, DatabaseTableHandler {
	/**
	 * Create the content table.
	 *
	 * @param Schema   $schema    Schema to add the content table to.
	 * @param callable $tableName Function to create a prefixed table name from a given table name.
	 * @return Schema
	 */
	public static function addTableToSchema(Schema $schema, callable $tableName): Schema {
		$table = $schema->createTable($tableName('content'));
		$table->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$table->addColumn('content_uuid', 'guid');
		$table->addColumn('site_uuid', 'guid');
		$table->addColumn('user_uuid', 'guid');
		$table->addColumn('content_obj', 'json');

		$table->setPrimaryKey(['dbid']);
		$table->addUniqueIndex(['content_uuid']);
		$table->addIndex(['site_uuid']);
		$table->addIndex(['user_uuid']);

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
	 * Find out if any content exists with this ID.
	 *
	 * @param UuidInterface $contentId ID to check.
	 * @return boolean
	 */
	public function hasContentWithId(UuidInterface $contentId): bool {
		$query = $this->db->createUnprefixedQueryBuilder();
		$query
			->select('1')
			->from($this->db->tableName('content'))
			->where('content_uuid = ?')
			->setParameter(0, $contentId);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Get the content object associated with the given ID.
	 *
	 * @param UuidInterface $contentId ID to retrieve.
	 * @return Content|null
	 */
	public function contentById(UuidInterface $contentId): ?Content {
		$query = $this->db->createUnprefixedQueryBuilder();
		$query
			->select('content_obj')
			->from($this->db->tableName('content'))
			->where('content_uuid = ?')
			->setParameter(0, $contentId);
		$result = $query->fetchOne();

		if ($result === false) {
			return null;
		}

		// This has to do with different DB engines which we cannot currently test.
		return is_string($result)
			? $this->serde->fromJson($result, Content::class)
			: $this->serde->fromArray($result, Content::class); // @codeCoverageIgnore
	}

	/**
	 * Retrieve a list of Content objects
	 *
	 * @param UuidInterface      $forSite     Content assigned to the given site.
	 * @param UuidInterface|null $ownedByUser Content owned by the given user.
	 * @return array Content objects meeting the given parameters.
	 */
	public function contentList(UuidInterface $forSite, ?UuidInterface $ownedByUser = null): array {
		$query = $this->db->createQueryBuilder();
		$query
			->select('content_obj')
			->from('content')
			->where('site_uuid = :site')
			->setParameter('site', $forSite)
			->orderBy('dbid', 'DESC');

		if (isset($ownedByUser)) {
			$query
				->andWhere('user_uuid = :user')
				->setParameter('user', $ownedByUser);
		}
		$results = $query->fetchFirstColumn();

		return array_map(
			fn($ser) => is_string($ser)
				? $this->serde->fromJson($ser, Content::class)
				: $this->serde->fromArray($ser, Content::class), // @codeCoverageIgnore
			$results,
		);
	}

	/**
	 * Create a new content entry.
	 *
	 * @param ContentCreated $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener()]
	public function onContentCreated(ContentCreated $event): void {
		$content = $event->getContentObject();

		$this->db->insert('content', [
			'content_uuid' => $content->id,
			'site_uuid' => $content->siteId,
			'user_uuid' => $content->userId,
			'content_obj' => $this->serde->toJson($content),
		]);
	}

	/**
	 * Update an existing content entry.
	 *
	 * @param ContentUpdated $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener()]
	public function onContentUpdated(ContentUpdated $event): void {
		$current = $this->contentById($event->entityId ?? UuidFactory::nil());
		if (!isset($current)) {
			return;
		}

		$updated = $current->with(
			body: $event->body,
			userId: $event->contentUserId ?? $event->userId,
			publishTimestamp: $event->publishTimestamp,
			extensions: $event->extensions,
		);
		$this->db->update(
			'content',
			['content_obj' => $this->serde->toJson($updated), 'user_uuid' => $updated->userId],
			['content_uuid' => $updated->id],
		);
	}

	/**
	 * Delete a content entry.
	 *
	 * @param ContentDeleted $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener()]
	public function onContentDeleted(ContentDeleted $event): void {
		$this->db->delete('content', ['content_uuid' => $event->entityId]);
	}

	/**
	 * Update a entry's canonical URL.
	 *
	 * @param ContentCanonicalUrlSet $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener()]
	public function onContentCanonicalUrlSet(ContentCanonicalUrlSet $event): void {
		$current = $this->contentById($event->entityId ?? UuidFactory::nil());
		if (!isset($current)) {
			return;
		}

		$updated = $current->with(canonicalUrl: $event->url);
		$this->db->update(
			'content',
			['content_obj' => $this->serde->toJson($updated)],
			['content_uuid' => $updated->id],
		);
	}

	/**
	 * Update an entry's links after a push.
	 *
	 * @param ContentPushSucceeded $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener()]
	public function onContentPushSucceeded(ContentPushSucceeded $event): void {
		$current = $this->contentById($event->contentId);
		if (!isset($current)) {
			return;
		}

		$pushInfo = $event->getEntryObject();
		$links = $current->links;

		$index = array_find_key($links, fn($entry) => $entry->id->equals($pushInfo->id)) ?? count($links);
		$links[$index] = $pushInfo;

		$updated = $current->with(links: $links);
		$this->db->update(
			'content',
			['content_obj' => $this->serde->toJson($updated)],
			['content_uuid' => $updated->id],
		);
	}
}
