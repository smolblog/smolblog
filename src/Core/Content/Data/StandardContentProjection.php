<?php

namespace Smolblog\Core\Content\Data;

use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\ContentBuilder;
use Smolblog\Core\Content\Events\{
	ContentBaseAttributeEdited,
	ContentBodyEdited,
	ContentCreated,
	ContentDeleted,
	ContentExtensionEdited,
	PermalinkAssigned,
	PublicContentAdded,
	PublicContentRemoved,
};
use Smolblog\Core\Content\GenericContent;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Framework\Messages\Attributes\ContentBuildLayerListener;
use Smolblog\Framework\Messages\Projection;
use Smolblog\Framework\Objects\Identifier;

/**
 * Project standard content properties.
 */
class StandardContentProjection implements Projection {
	public const TABLE = 'standard_content';

	/**
	 * Construct the service.
	 *
	 * @param ConnectionInterface $db Working DB connection.
	 */
	public function __construct(
		private ConnectionInterface $db,
	) {
	}

	/**
	 * Add new content.
	 *
	 * @param ContentCreated $event Event to handle.
	 * @return void
	 */
	public function onContentCreated(ContentCreated $event) {
		$this->db->table(self::TABLE)->insert([
			'content_uuid' => $event->contentId->toString(),
			'type' => $event->getContentType(),
			'title' => $event->getNewTitle(),
			'body' => $event->getNewBody(),
			'site_uuid' => $event->siteId->toString(),
			'author_uuid' => $event->authorId->toString(),
			'publish_timestamp' => $event->publishTimestamp?->format(DateTimeInterface::RFC3339_EXTENDED) ?? null,
			'visibility' => ContentVisibility::Draft->value,
			'extensions' => '[]'
		]);
	}

	/**
	 * Handle content changing.
	 *
	 * @param ContentBodyEdited $event Event to handle.
	 * @return void
	 */
	public function onContentBodyEdited(ContentBodyEdited $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'title' => $event->getNewTitle(),
			'body' => $event->getNewBody(),
		]);
	}

	/**
	 * Delete content
	 *
	 * @param ContentDeleted $event Event to handle.
	 * @return void
	 */
	public function onContentDeleted(ContentDeleted $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->delete();
	}

	/**
	 * Handle a base attribute being edited.
	 *
	 * These are currently the author and publish timestamps.
	 *
	 * @param ContentBaseAttributeEdited $event Event to handle.
	 * @return void
	 */
	public function onContentBaseAttributeEdited(ContentBaseAttributeEdited $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'author_uuid' => $event->authorId?->toString() ?? null,
			'publish_timestamp' => $event->publishTimestamp?->format(DateTimeInterface::RFC3339_EXTENDED) ?? null,
		]);
	}

	/**
	 * Handle a content extension changing.
	 *
	 * @param ContentExtensionEdited $event Event to handle.
	 * @return void
	 */
	public function onContentExtensionEdited(ContentExtensionEdited $event) {
		$currentJson = $this->db->table(self::TABLE)->
			where('content_uuid', '=', $event->contentId->toString())->
			value('extensions') ?? '[]';

		$current = json_decode($currentJson, true) ?? [];
		$ext = $event->getNewExtension();
		$current[get_class($ext)] = $ext->toArray();

		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'extensions' => json_encode($current)
		]);
	}

	/**
	 * Save the permalink that has been assigned.
	 *
	 * Normally this would be a "standard property" but not right now.
	 *
	 * @param PermalinkAssigned $event Event to handle.
	 * @return void
	 */
	public function onPermalinkAssigned(PermalinkAssigned $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'permalink' => $event->permalink
		]);
	}

	/**
	 * Handle content being published.
	 *
	 * When content is made public, its visibility needs to be updated. Also, if it does not have a publish time, it
	 * should be given one.
	 *
	 * @param PublicContentAdded $event Event to handle.
	 * @return void
	 */
	#[ContentBuildLayerListener(earlier: 5)]
	public function onPublicContentAdded(PublicContentAdded $event) {
		$pubDate = $this->db->table(self::TABLE)->
			where('content_uuid', '=', $event->contentId->toString())->
			value('publish_timestamp');

		$update = ['visibility' => ContentVisibility::Published->value];
		if (!isset($pubDate)) {
			$update['publish_timestamp'] = $event->timestamp->format(DateTimeInterface::RFC3339_EXTENDED);
		}

		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update($update);
	}

	/**
	 * Handle unpublishing content.
	 *
	 * This does not necessarily mean "deleted"!
	 *
	 * @param PublicContentRemoved $event Event to handle.
	 * @return void
	 */
	#[ContentBuildLayerListener(earlier: 5)]
	public function onPublicContentRemoved(PublicContentRemoved $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'visibility' => ContentVisibility::Draft->value,
		]);
	}

	/**
	 * Add the standard properties to a Content Builder
	 *
	 * @param ContentBuilder $message ContentBuilder message.
	 * @return void
	 */
	#[ContentBuildLayerListener(later:5)]
	public function onContentBuilder(ContentBuilder $message) {
		$results = $this->db->table(self::TABLE)->
			where('content_uuid', '=', $message->getContentId()->toString())->first();

		$message->setContentProperty(
			id: $message->getContentId(),
			siteId: isset($results->site_uuid) ? Identifier::fromString($results->site_uuid) : null,
			authorId: isset($results->author_uuid) ? Identifier::fromString($results->author_uuid) : null,
			permalink: $results->permalink ?? null,
			publishTimestamp: isset($results->publish_timestamp) ?
				new DateTimeImmutable($results->publish_timestamp) : null,
			visibility: ContentVisibility::tryFrom($results->visibility ?? ''),
		);

		$extensions = json_decode($results->extensions ?? '[]', true);
		foreach ($extensions as $ext_class => $ext_array) {
			$ext = $ext_class::fromArray($ext_array);
			$message->addContentExtension($ext);
		}
	}

	/**
	 * Add the GenericContent type to a GenericContentById query.
	 *
	 * @param GenericContentById $query Query to handle.
	 * @return void
	 */
	#[ContentBuildLayerListener]
	public function onGenericContentById(GenericContentById $query) {
		$results = $this->db->table(self::TABLE)->
			where('content_uuid', '=', $query->contentId->toString())->first();

		$query->setContentType(new GenericContent(title: $results->title, body: $results->body));
	}
}
