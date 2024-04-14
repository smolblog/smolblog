<?php

namespace Smolblog\Core\ContentV1\Media;

use DateTimeInterface;
use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\ContentV1\Queries\ContentVisibleToUser;
use Smolblog\Framework\Messages\Attributes\ContentBuildLayerListener;
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
			'uploaded_at' => $event->timestamp->format(DateTimeInterface::RFC3339_EXTENDED),
		]);
	}

	/**
	 * Handle editing media attributes.
	 *
	 * @param MediaAttributesEdited $event Event to handle.
	 * @return void
	 */
	public function onMediaAttributesEdited(MediaAttributesEdited $event) {
		$this->db->table(self::TABLE)->
			where('content_uuid', '=', $event->contentId->toString())->
			update(array_filter([
				'title' => $event->title,
				'accessibility_text' => $event->accessibilityText,
			]));
	}

	/**
	 * Handle deleting media.
	 *
	 * @param MediaDeleted $event Event to handle.
	 * @return void
	 */
	public function onMediaDeleted(MediaDeleted $event) {
		$this->db->table(self::TABLE)->
			where('content_uuid', '=', $event->contentId->toString())->
			delete();
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
	 * Get the list of available media.
	 *
	 * @param MediaList $query Query to execute.
	 * @return void
	 */
	public function onMediaList(MediaList $query) {
		$builder = $this->db->table(self::TABLE)
			->where('site_uuid', '=', $query->siteId->toString())
			->orderByDesc('uploaded_at');

		if (isset($query->types)) {
			$builder = $builder->whereIn('type', $query->types);
		}

		$query->count = $builder->count();

		$builder = $builder->skip(($query->page - 1) * $query->pageSize)->take($query->pageSize);

		$query->setResults($builder->get()->map(
			fn($row) => $this->mediaFromRow($row)
		)->toArray());
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
	 * Turn media IDs into actual entities.
	 *
	 * @param NeedsMediaObjects $message Message with Media IDs.
	 * @return void
	 */
	#[ContentBuildLayerListener(earlier: 10)]
	public function onNeedsMediaObjects(NeedsMediaObjects $message) {
		$results = $this->db->table(self::TABLE)->whereIn('content_uuid', $message->getMediaIds())->get();

		$message->setMediaObjects(
			array_map(
				fn($id) => self::mediaFromRow($results->firstWhere('content_uuid', $id->toString())),
				$message->getMediaIds()
			)
		);
	}

	/**
	 * Handle finding Media by its default URL.
	 *
	 * This is used when the API receives a URL instead of an ID.
	 *
	 * @param MediaByDefaultUrl $query Query to execute.
	 * @return void
	 */
	public function onMediaByDefaultUrl(MediaByDefaultUrl $query) {
		$row = $this->db->table(self::TABLE)->where('default_url', '=', $query->url)->first();

		$query->setResults(isset($row) ? self::mediaFromRow($row) : null);
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
