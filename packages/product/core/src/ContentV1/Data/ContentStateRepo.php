<?php

namespace Smolblog\Core\ContentV1\Data;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\ContentV1\Content;
use Smolblog\Framework\Messages\Projection;
use Smolblog\Framework\Objects\Identifier;

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

	/**
	 * Get a single content object.
	 *
	 * @param Identifier $id ID of the content to get.
	 * @return Content
	 */
	public function getSingleContent(Identifier $id): Content {
		$row = $this->db->table(self::TABLE)->where('content_uuid', $id->toString())->value('content');

		return Content::jsonDeserialize($row);
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

		return array_map(fn($row) => Content::jsonDeserialize($row->content), $rows->all());
	}

	/**
	 * Save one or more content objects.
	 *
	 * @param Content[] $content Array of content objects to save.
	 * @return void
	 */
	public function setContent(array $content): void {
		$this->db->table(self::TABLE)->upsert(
			array_map(
				fn($c) => ['content_uuid' => $c->id->toString(), 'content' => json_encode($c)],
				$content,
			),
			'content_uuid',
			['content'],
		);
	}
}
