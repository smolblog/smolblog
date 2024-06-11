<?php

namespace Smolblog\Core\Content;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\Content;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Queries\ContentById;
use Smolblog\Core\Content\Queries\ContentVisibleToUser;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Service\Event\EventListenerService;
use Smolblog\Foundation\Service\Event\ProjectionListener;
use Smolblog\Foundation\Service\Messaging\ExecutionListener;
use Smolblog\Foundation\Service\Messaging\Projection;
use Smolblog\Foundation\Service\Query\QueryBus;
use Smolblog\Foundation\Service\Query\QueryHandler;
use Smolblog\Foundation\Service\Query\QueryHandlerService;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Store content objects in a simple key-value store.
 *
 * Not every content type or extension needs a full relational database. This is a simple table that stores
 * the serialized content objects. If a type or exenstion doesn't need anything else, no more needs to be written.
 * Otherwise, a separate projection can be created to handle the specific use case.
 */
class ContentStateRepo implements EventListenerService, QueryHandlerService {
	public const TABLE = 'content_states';

	/**
	 * Construct the projection.
	 *
	 * @param ConnectionInterface $db  Working database connection.
	 * @param QueryBus            $bus Active QueryBus.
	 */
	public function __construct(
		private ConnectionInterface $db,
		private QueryBus $bus,
	) {
	}

	/**
	 * Check if the given ID corresponds to an existing piece of Content. Used to check edits and prevent colissions.
	 *
	 * @param Identifier $id ID of the Content.
	 * @return boolean True if Content with that ID exists.
	 */
	public function contentExists(Identifier $id): bool {
		return $this->db->table(self::TABLE)->where('content_uuid', $id->toString())->exists();
	}

	/**
	 * Get a single content object.
	 *
	 * @param Identifier $id ID of the content to get.
	 * @return Content|null
	 */
	public function getSingleContent(Identifier $id): ?Content {
		$row = $this->db->table(self::TABLE)->where('content_uuid', $id->toString())->value('content');

		return isset($row) ? Content::fromJson($row) : null;
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

	/**
	 * Respond to the ContentCreated event.
	 *
	 * @param ContentCreated $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onContentCreated(ContentCreated $event): void {
		$this->setContent([$event->content]);
	}

	/**
	 * Respond to the ContentUpdated event.
	 *
	 * @param ContentUpdated $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onContentUpdated(ContentUpdated $event): void {
		$this->setContent([$event->content]);
	}

	/**
	 * Respond to the ContentDeleted event.
	 *
	 * @param ContentDeleted $event Event to handle.
	 * @return void
	 */
	#[ProjectionListener]
	public function onContentDeleted(ContentDeleted $event): void {
		$this->db->table(self::TABLE)->where('content_uuid', $event->entityId->toString())->delete();
	}

	/**
	 * Respond to the ContentById query.
	 *
	 * @param ContentById $query Query to execute.
	 * @return void
	 */
	#[QueryHandler]
	public function onContentById(ContentById $query): void {
		$query->setResults($this->getSingleContent($query->id));
	}

	/**
	 * Respond to the ContentVisibleToUser query.
	 *
	 * @param ContentVisibleToUser $query Query to execute.
	 * @return void
	 */
	#[QueryHandler]
	public function onContentVisibleToUser(ContentVisibleToUser $query): void {
		$query->setResults($this->checkUserAndContent(contentId: $query->contentId, userId: $query->userId));
	}

	/**
	 * Respond to the UserCanEditContent query.
	 *
	 * @param UserCanEditContent $query Query to execute.
	 * @return void
	 */
	#[QueryHandler]
	public function onUserCanEditContent(UserCanEditContent $query): void {
		$query->setResults(
			$this->checkUserAndContent(contentId: $query->contentId, userId: $query->userId, needsEdit: true)
		);
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

	/**
	 * Check the permissions related to this particular User and Content.
	 *
	 * @param Identifier      $contentId ID of the Content to check.
	 * @param Identifier|null $userId    ID of the User to check; null for anonymous.
	 * @param boolean         $needsEdit True if the user needs to be able to edit the content.
	 * @return boolean
	 */
	private function checkUserAndContent(
		Identifier $contentId,
		?Identifier $userId = null,
		bool $needsEdit = false
	): bool {
		$content = $this->getSingleContent($contentId);
		if (!isset($content)) {
			return false;
		}
		if (($content->published && !$needsEdit) || ($content->authorId == $userId)) {
			return true;
		}

		return isset($userId) && $this->bus->fetch(new UserHasPermissionForSite(
			siteId: $content->siteId,
			userId: $userId,
			mustBeAdmin: true,
		));
	}
}
