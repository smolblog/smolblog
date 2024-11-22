<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Data\ContentStateManager;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\{ContentCanonicalUrlSet, ContentCreated, ContentDeleted, ContentUpdated};
use Smolblog\Foundation\Service\Event\ProjectionListener;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Store and retrieve Content objects.
 */
class ContentProjection implements ContentRepo, ContentStateManager, DatabaseTableHandler {
	/**
	 * Create the content table.
	 *
	 * @param Schema $schema Schema to add the content table to.
	 * @return Schema
	 */
	public static function addTableToSchema(Schema $schema): Schema {
		$table = $schema->createTable('content');
		$table->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$table->addColumn('content_uuid', 'guid');
		$table->addColumn('site_uuid', 'guid');
		$table->addColumn('content_obj', 'json');

		$table->setPrimaryKey(['dbid']);
		$table->addUniqueIndex(['content_uuid']);
		$table->addIndex(['site_uuid']);

		return $schema;
	}

	/**
	 * Create the service.
	 *
	 * @param Connection $db Working database connection.
	 */
	public function __construct(private Connection $db) {
	}

	/**
	 * Find out if any content exists with this ID.
	 *
	 * @param Identifier $contentId ID to check.
	 * @return boolean
	 */
	public function hasContentWithId(Identifier $contentId): bool {
		$query = $this->db->createQueryBuilder();
		$query->select('1')->from('content')->where('content_uuid = ?')->setParameter(0, $contentId);
		$result = $query->fetchOne();

		return $result ? true : false;
	}

	/**
	 * Get the content object associated with the given ID.
	 *
	 * @param Identifier $contentId ID to retrieve.
	 * @return Content|null
	 */
	public function contentById(Identifier $contentId): ?Content {
		$query = $this->db->createQueryBuilder();
		$query->select('content_obj')->from('content')->where('content_uuid = ?')->setParameter(0, $contentId);
		$result = $query->fetchOne();

		if ($result === false) {
			return null;
		}

		// This has to do with different DB engines which we cannot currently test.
		return is_string($result) ?
			Content::fromJson($result) :
			Content::deserializeValue($result); // @codeCoverageIgnore
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
				'content_obj' => json_encode($content),
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
		$current = $this->contentById($event->entityId ?? Identifier::nil());
		if (!isset($current)) {
			return;
		}

		$updated = $current->with(
			body: $event->body,
			userId: $event->contentUserId ?? $event->userId,
			publishTimestamp: $event->publishTimestamp,
			extensions: $event->extensions,
		);
		$this->db->update('content', ['content_obj' => json_encode($updated)], ['content_uuid' => $updated->id]);
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
		$current = $this->contentById($event->entityId ?? Identifier::nil());
		if (!isset($current)) {
			return;
		}

		$updated = $current->with(canonicalUrl: $event->url);
		$this->db->update('content', ['content_obj' => json_encode($updated)], ['content_uuid' => $updated->id]);
	}

	/**
	 * Update an entry's links after a push.
	 *
	 * @param ContentPushedToChannel $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener()]
	public function onContentPushedToChannel(ContentPushedToChannel $event): void {
		$current = $this->contentById($event->content->id);
		if (!isset($current)) {
			return;
		}

		$pushInfo = $event->getEntryObject();
		$links = $current->links;
		$links[$pushInfo->getId()->toString()] = $pushInfo;

		$updated = $current->with(links: $links);
		$this->db->update('content', ['content_obj' => json_encode($updated)], ['content_uuid' => $updated->id]);
	}
}
