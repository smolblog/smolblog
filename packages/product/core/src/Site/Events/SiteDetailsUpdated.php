<?php

namespace Smolblog\Core\Site\Events;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates that the details of a Site have been updated with new values.
 */
readonly class SiteDetailsUpdated extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @throws InvalidValueProperties When no updated attributes are provided.
	 *
	 * @param Identifier         $userId      User making the change.
	 * @param Identifier         $aggregateId Site being changed.
	 * @param string|null        $displayName New title for the site; null for no change.
	 * @param string|null        $description New description for the site; null for no change.
	 * @param Identifier|null    $pictureId   New picture for the site; null for no change.
	 * @param Identifier|null    $id          ID of the event.
	 * @param DateTimeField|null $timestamp   Timestamp of the event.
	 * @param Identifier|null    $processId   Optional process that created this event.
	 */
	public function __construct(
		Identifier $userId,
		Identifier $aggregateId,
		public ?string $displayName = null,
		public ?string $description = null,
		public ?Identifier $pictureId = null,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $processId = null,
	) {
		if (!isset($displayName) && !isset($description) && !isset($pictureId)) {
			throw new InvalidValueProperties(message: 'No updated attributes provided.');
		}

		parent::__construct(
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
			aggregateId: $aggregateId,
			processId: $processId,
		);
	}

	/**
	 * Remove 'entityId' from (de)serialization.
	 *
	 * @return array
	 */
	protected static function propertyInfo(): array {
		$base = parent::propertyInfo();
		unset($base['entityId']);
		return $base;
	}
}
