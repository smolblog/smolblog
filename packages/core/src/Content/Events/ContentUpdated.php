<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Foundation\Value\Traits\ArrayType;

/**
 * Something in a piece of content has been changed. Updates the content to match all values here; any values omitted
 * should be considered removed.
 */
readonly class ContentUpdated extends DomainEvent {
	/**
	 * Undocumented function
	 *
	 * @param ContentType        $body             Body of the content.
	 * @param Identifier         $aggregateId      Site the content belongs to.
	 * @param Identifier         $userId           User making the change.
	 * @param Identifier         $entityId         ID of the content object.
	 * @param Identifier|null    $id               ID for this event.
	 * @param DateTimeField|null $timestamp        Timestamp for this event.
	 * @param Identifier|null    $contentUserId    User responsible for the content if not $userId.
	 * @param DateTimeField|null $publishTimestamp Time and date the content was first published.
	 * @param ContentExtension[] $extensions       Extensions on the content.
	 */
	public function __construct(
		public ContentType $body,
		Identifier $aggregateId,
		Identifier $userId,
		Identifier $entityId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		public ?Identifier $contentUserId = null,
		public ?DateTimeField $publishTimestamp = null,
		#[ArrayType(ContentExtension::class)] public array $extensions = [],
	) {
		parent::__construct(
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
			aggregateId: $aggregateId,
			entityId: $entityId,
		);
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
