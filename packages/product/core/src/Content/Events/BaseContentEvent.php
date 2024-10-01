<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Core\Content;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Base logic for Content events.
 *
 * Since these events have the full Content object, the aggregate and entity IDs are inferred from the Content. While
 * they are serialized normally, they should be ignored in deserialization.
 */
readonly abstract class BaseContentEvent extends DomainEvent {
	/**
	 * Construct the event.
	 *
	 * @param Content            $content   Content as of this event.
	 * @param Identifier         $userId    User triggering the event.
	 * @param Identifier|null    $id        ID of the event.
	 * @param DateTimeField|null $timestamp Time of the event.
	 */
	public function __construct(
		public Content $content,
		Identifier $userId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		parent::__construct(
			id: $id ?? new DateIdentifier(),
			timestamp: $timestamp ?? new DateTimeField(),
			userId: $userId,
			aggregateId: $content->siteId,
			entityId: $content->id,
		);
	}

	/**
	 * Deserialize the object.
	 *
	 * Removes 'entityId' and 'aggregateId' as they are populated from 'content'.
	 *
	 * @param array $data Serialized data.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		unset($data['entityId'], $data['aggregateId']);
		return parent::deserializeValue($data);
	}
}
