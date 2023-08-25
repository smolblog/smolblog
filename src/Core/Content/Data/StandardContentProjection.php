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
use Smolblog\Core\Content\Queries\{
	ContentByPermalink,
	ContentVisibleToUser,
	GenericContentBuilder,
	UserCanEditContent
};
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\Attributes\ContentBuildLayerListener;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\MessageBus;
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
	 * @param ConnectionInterface $db  Working DB connection.
	 * @param MessageBus          $bus Active MessageBus.
	 */
	public function __construct(
		private ConnectionInterface $db,
		private MessageBus $bus,
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
	#[ExecutionLayerListener(later: 5)]
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
	 * @param GenericContentBuilder $query Query to handle.
	 * @return void
	 */
	#[ContentBuildLayerListener]
	public function onGenericContentBuilder(GenericContentBuilder $query) {
		$results = $this->db->table(self::TABLE)->
			where('content_uuid', '=', $query->getContentId()->toString())->first();

		$query->setContentType(new GenericContent(title: $results->title, body: $results->body));
	}

	/**
	 * Check if the user can see the content.
	 *
	 * Will be true if:
	 * 1. The content is public.
	 * 2. The user is the author.
	 * 3. The user is an admin.
	 *
	 * @param ContentVisibleToUser $query Query to fetch.
	 * @return void
	 */
	public function onContentVisibleToUser(ContentVisibleToUser $query) {
		$isPublic = $this->db->table(self::TABLE)->
			where('content_uuid', '=', $query->contentId->toString())->value('visibility');

		$query->setResults($isPublic === ContentVisibility::Published->value ? true : $this->checkContentPerm(
			contentId: $query->contentId,
			siteId: $query->siteId,
			userId: $query->userId,
		));
	}

	/**
	 * Check if the user can edit the content.
	 *
	 * Will be true if:
	 * 2. The user is the author.
	 * 3. The user is an admin.
	 *
	 * @param UserCanEditContent $query Query to fetch.
	 * @return void
	 */
	public function onUserCanEditContent(UserCanEditContent $query) {
		$query->setResults($this->checkContentPerm(
			contentId: $query->contentId,
			siteId: $query->siteId,
			userId: $query->userId,
		));
	}

	/**
	 * Find info for a Content piece by permalink.
	 *
	 * This is an AdaptableQuery, so it will be picked up by the ContentService after this which will dispatch the
	 * query for the content's particular type.
	 *
	 * @param ContentByPermalink $query Query to execute.
	 * @return void
	 */
	public function onContentByPermalink(ContentByPermalink $query) {
		$result = $this->db->table('standard_content')->
			where('site_uuid', '=', $query->siteId->toString())->
			where('permalink', '=', $query->permalink)->
			first(['content_uuid', 'type']);

		if (isset($result)) {
			$query->setContentInfo(id: Identifier::fromString($result->content_uuid), type: $result->type);
		}
	}

	/*
		Commenting out until this is needed. Because I don't want to test this yet.
		public function onContentList(ContentList $query) {
			$isAdmin = isset($query->userId) && $this->bus->fetch(
				new UserHasPermissionForSite(siteId: $query->siteId, userId: $query->userId, mustBeAdmin: true)
			);

			$builder = $this->db->table(self::TABLE)
				->where('site_uuid', '=', $query->siteId->toString())
				->orderByDesc('publish_timestamp')
				->skip(($query->page - 1) * $query->pageSize)->take($query->pageSize);

			if (!$isAdmin) {
				$builder = $builder->where(
					fn($q) => $q->where('author_uuid', '=', $query->userId->toString())
											->orWhere('visibility', '=', ContentVisibility::Published->value)
				);
			}

			if (isset($query->types)) {
				$builder = $builder->whereIn('type', $query->types);
			}
			if (isset($query->visibility)) {
				$builder = $builder->whereIn('visibility', $query->visibility);
			}

			$query->setResults($builder->get()->map(
				fn($row) => new Content(
					id: Identifier::fromString($row->content_uuid),
					type: new GenericContent(title: $row->title, body: $row->body, typeClass: $row->type),
					siteId: Identifier::fromString($row->site_uuid),
					authorId: Identifier::fromString($row->author_uuid),
					permalink: $row->permalink ?? null,
					publishTimestamp: isset($row->publish_timestamp) ?
						new DateTimeImmutable($row->publish_timestamp) : null,
					visibility: ContentVisibility::tryFrom($row->visibility),
				)
			));
		}
	*/

	/**
	 * Check if the given user either owns the content or has admin priveleges.
	 *
	 * @param Identifier $contentId ID of the content.
	 * @param Identifier $siteId    Site the content belongs to.
	 * @param Identifier $userId    User making the request.
	 * @return boolean
	 */
	private function checkContentPerm(Identifier $contentId, Identifier $siteId, ?Identifier $userId): bool {
		if (!isset($userId)) {
			return false;
		}

		$is_author = $this->db->table(self::TABLE)->where([
			['content_uuid', '=', $contentId->toString()],
			['site_uuid', '=', $siteId->toString()],
			['author_uuid', '=', $userId->toString()],
		])->exists();

		return $is_author || $this->bus->fetch(
			new UserHasPermissionForSite(siteId: $siteId, userId: $userId, mustBeAdmin: true)
		);
	}
}
