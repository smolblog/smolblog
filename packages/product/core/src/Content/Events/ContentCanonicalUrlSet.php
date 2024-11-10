<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates that a canonical URL has been set for this content.
 *
 * This should usually be a personal website or other user-controlled page. Once the content is pushed to that channel,
 * the handler should dispatch this event to update the Content state. Other channels may use this to post links back
 * to the original/canonical store (for example, a Note may be posted in full while a longer Article is only linked to
 * from an external channel).
 */
readonly class ContentCanonicalUrlSet extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Url                $url         New canonical URL for the Content.
	 * @param Identifier         $aggregateId Site the content belongs to.
	 * @param Identifier         $userId      User making the change.
	 * @param Identifier         $entityId    ID of the content object.
	 * @param Identifier|null    $id          ID for this event.
	 * @param DateTimeField|null $timestamp   Timestamp for this event.
	 * @param Identifier|null    $processId   Optional process ID this belongs to.
	 */
	public function __construct(
		public Url $url,
		Identifier $aggregateId,
		Identifier $userId,
		Identifier $entityId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $processId = null,
	) {
		parent::__construct(
			aggregateId: $aggregateId,
			userId: $userId,
			entityId: $entityId,
			id: $id,
			timestamp: $timestamp,
			processId: $processId,
		);
	}
}
