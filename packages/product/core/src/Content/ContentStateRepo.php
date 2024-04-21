<?php

namespace Smolblog\Core\Content;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Content;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Queries\ContentById;
use Smolblog\Foundation\Service\Messaging\ExecutionListener;
use Smolblog\Foundation\Service\Messaging\Projection;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Store content objects in a simple key-value store.
 *
 * Not every content type or extension needs a full relational database. This is a simple table that stores
 * the serialized content objects. If a type or exenstion doesn't need anything else, no more needs to be written.
 * Otherwise, a separate projection can be created to handle the specific use case.
 */
class ContentStateRepo implements Projection {
	public const TABLE = 'content_states';

	/**
	 * Construct the projection.
	 *
	 * @param ConnectionInterface $db Working database connection.
	 */
	public function __construct(
		private ConnectionInterface $db,
	) {
	}

	public function contentExists(Identifier $id): bool {
		return $this->db->table(self::TABLE)->where('content_uuid', $id->toString())->exists();
	}

	/**
	 * Get a single content object.
	 *
	 * @param Identifier $id ID of the content to get.
	 * @return Content
	 */
	public function getSingleContent(Identifier $id): Content {
		$row = $this->db->table(self::TABLE)->where('content_uuid', $id->toString())->value('content');

		return Content::fromJson($row);
	}

	/**
	 * Get many content objects.
	 *
	 * @param Identifier[] $ids Array of content IDs to get.
	 * @return Content[]
	 */
	public function getMultipleContent(array $ids): array {
		$rows = $this->db->table(self::TABLE)->
			whereIn('content_uuid', array_map(fn($id) => $id->toString(), $ids))->
			get();

		return array_map(fn($row) => Content::fromJson($row->content), $rows->all());
	}

	#[ExecutionListener]
	public function onContentCreated(ContentCreated $event): void {
		$this->setContent([$event->content]);
	}

	#[ExecutionListener]
	public function onContentUpdated(ContentUpdated $event): void {
		$this->setContent([$event->content]);
	}

	#[ExecutionListener]
	public function onContentDeleted(ContentDeleted $event): void {
		$this->db->table(self::TABLE)->where('content_uuid', $event->entityId->toString())->delete();
	}

	#[ExecutionListener]
	public function onContentById(ContentById $query): void {
		$result = $this->db->table(self::TABLE)->where('content_uuid', $query->id->toString())->first();
		if (isset($result)) {
			$query->setResults(Content::fromJson($result->content));
		}
	}

	/**
	 * Save one or more content objects.
	 *
	 * @param Content[] $content Array of content objects to save.
	 * @return void
	 */
	private function setContent(array $content): void {
		$this->db->table(self::TABLE)->upsert(
			array_map(
				fn($c) => [
					'content_uuid' => $c->id->toString(),
					'site_uuid' => $c->siteId->toString(),
					'author_uuid' => $c->authorId->toString(),
					'content' => json_encode($c)
				],
				$content,
			),
			'content_uuid',
			['content'],
		);
	}
}
