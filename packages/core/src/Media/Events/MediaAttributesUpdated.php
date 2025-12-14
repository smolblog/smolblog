<?php

namespace Smolblog\Core\Media\Events;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicate that attributes have been changed on a piece of media.
 */
readonly class MediaAttributesUpdated extends DomainEvent {
	/**
	 * Construct the event.
	 *
	 * @throws InvalidValueProperties Thrown if no updated attributes are given.
	 *
	 * @param Identifier         $entityId          ID of the Media object.
	 * @param Identifier         $userId            User uploading the media.
	 * @param Identifier         $aggregateId       Site media is being uploaded to.
	 * @param string|null        $title             Updated title of the media.
	 * @param string|null        $accessibilityText Updated text-only description of the media.
	 * @param Identifier|null    $id                ID of the event.
	 * @param DateTimeField|null $timestamp         Timestamp of the event.
	 */
	public function __construct(
		Identifier $entityId,
		Identifier $userId,
		Identifier $aggregateId,
		public ?string $title = null,
		public ?string $accessibilityText = null,
		?Identifier $id = null,
		?DateTimeField $timestamp = null
	) {
		if (!isset($title) && !isset($accessibilityText)) {
			throw new InvalidValueProperties('No updated attributes provided.');
		}
		if ((isset($title) && empty($title)) || (isset($accessibilityText) && empty($accessibilityText))) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}

		parent::__construct(
			id: $id,
			timestamp: $timestamp,
			userId: $userId,
			aggregateId: $aggregateId,
			entityId: $entityId,
		);
	}
}
