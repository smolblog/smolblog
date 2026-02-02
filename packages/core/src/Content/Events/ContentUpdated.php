<?php

namespace Smolblog\Core\Content\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Reflection\ListType;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Content\Entities\ContentType;

/**
 * Something in a piece of content has been changed. Updates the content to match all values here; any values omitted
 * should be considered removed.
 */
class ContentUpdated implements DomainEvent {
	use DomainEventKit;

	/**
	 * Undocumented function
	 *
	 * @param ContentType            $body             Body of the content.
	 * @param UuidInterface          $aggregateId      Site the content belongs to.
	 * @param UuidInterface          $userId           User making the change.
	 * @param UuidInterface          $entityId         ID of the content object.
	 * @param UuidInterface|null     $id               ID for this event.
	 * @param DateTimeInterface|null $timestamp        Timestamp for this event.
	 * @param UuidInterface|null     $processId        Process responsible for the event.
	 * @param UuidInterface|null     $contentUserId    User responsible for the content if not $userId.
	 * @param DateTimeInterface|null $publishTimestamp Time and date the content was first published.
	 * @param ContentExtension[]     $extensions       Extensions on the content.
	 */
	public function __construct(
		public readonly ContentType $body,
		public readonly UuidInterface $aggregateId,
		public readonly UuidInterface $userId,
		public readonly UuidInterface $entityId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
		public readonly ?UuidInterface $contentUserId = null,
		public readonly ?DateTimeInterface $publishTimestamp = null,
		#[ListType(ContentExtension::class)] public array $extensions = [],
	) {
		$this->setIdAndTime($id, $timestamp);
	}

	/**
	 * Create a Content object based on the information in this event.
	 *
	 * @return Content
	 */
	public function getContentObject(): Content {
		return new Content(
			body: $this->body,
			siteId: $this->aggregateId,
			userId: $this->contentUserId ?? $this->userId,
			id: $this->entityId,
			publishTimestamp: $this->publishTimestamp,
			extensions: $this->extensions,
		);
	}
}
