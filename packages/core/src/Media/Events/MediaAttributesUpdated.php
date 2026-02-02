<?php

namespace Smolblog\Core\Media\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Validation\Validated;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Indicate that attributes have been changed on a piece of media.
 */
class MediaAttributesUpdated implements DomainEvent, Validated {
	use DomainEventKit;

	/**
	 * Construct the event.
	 *
	 * @throws InvalidValueProperties Thrown if no updated attributes are given.
	 *
	 * @param UuidInterface          $entityId          ID of the Media object.
	 * @param UuidInterface          $userId            User uploading the media.
	 * @param UuidInterface          $aggregateId       Site media is being uploaded to.
	 * @param string|null            $title             Updated title of the media.
	 * @param string|null            $accessibilityText Updated text-only description of the media.
	 * @param UuidInterface|null     $id                ID of the event.
	 * @param DateTimeInterface|null $timestamp         Timestamp of the event.
	 * @param UuidInterface|null     $processId         ID of the process responsible for this event.
	 */
	public function __construct(
		public readonly UuidInterface $entityId,
		public readonly UuidInterface $userId,
		public readonly UuidInterface $aggregateId,
		public readonly ?string $title = null,
		public readonly ?string $accessibilityText = null,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->setIdAndTime($id, $timestamp);
		$this->validate();
	}

	public function validate(): void {
		if (!isset($this->title) && !isset($this->accessibilityText)) {
			throw new InvalidValueProperties('No updated attributes provided.');
		}
		if (
			(isset($this->title) && empty($this->title))
			|| (isset($this->accessibilityText) && empty($this->accessibilityText))
		) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}
	}
}
