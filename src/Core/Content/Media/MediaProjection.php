<?php

namespace Smolblog\Core\Content\Media;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Content\Queries\ContentVisibleToUser;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\Projection;
use Smolblog\Framework\Objects\Identifier;
use stdClass;

/**
 * Store state for Media objects.
 */
class MediaProjection implements Projection {
	public const TABLE = 'media';

	/**
	 * Construct the projection
	 *
	 * @param ConnectionInterface $db Working database connection.
	 */
	public function __construct(
		private ConnectionInterface $db
	) {
	}

	/**
	 * Handle the MediaAdded event.
	 *
	 * @param MediaAdded $event Event to handle.
	 * @return void
	 */
	public function onMediaAdded(MediaAdded $event) {
		$this->db->table(self::TABLE)->insert([
			'content_uuid' => $event->contentId->toString(),
			'user_uuid' => $event->userId->toString(),
			'site_uuid' => $event->siteId->toString(),
			'title' => $event->title,
			'accessibility_text' => $event->accessibilityText,
			'type' => $event->type->value,
			'thumbnail_url' => $event->thumbnailUrl,
			'default_url' => $event->defaultUrl,
			'file' => json_encode($event->file),
		]);
	}

	/**
	 * Handle the MediaById query
	 *
	 * @param MediaById $query Query to answer.
	 * @return void
	 */
	public function onMediaById(MediaById $query) {
		$row = $this->db->table(self::TABLE)->where('content_uuid', '=', $query->contentId->toString())->first();

		$query->setResults(isset($row) ? self::mediaFromRow($row) : null);
	}

	/**
	 * Intercept the ContentVisibleToUser query to check if it is a media object.
	 *
	 * @param ContentVisibleToUser $query Query to check.
	 * @return void
	 */
	#[ExecutionLayerListener(earlier: 5)]
	public function onContentVisibleToUser(ContentVisibleToUser $query) {
		// Media objects are always public for now.
		if ($this->db->table(self::TABLE)->where('content_uuid', '=', $query->contentId->toString())->exists()) {
			$query->setResults(true);
			$query->stopMessage();
		}
	}

	/**
	 * Create a Media object from a database row.
	 *
	 * @param stdClass $row Database data.
	 * @return Media
	 */
	public static function mediaFromRow(stdClass $row): Media {
		return new Media(
			id: Identifier::fromString($row->content_uuid),
			userId: Identifier::fromString($row->user_uuid),
			siteId: Identifier::fromString($row->site_uuid),
			title: $row->title,
			accessibilityText: $row->accessibility_text,
			type: MediaType::tryFrom($row->type),
			thumbnailUrl: $row->thumbnail_url,
			defaultUrl: $row->default_url,
			file: MediaFile::jsonDeserialize($row->file),
		);
	}
}
