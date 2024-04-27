<?php

namespace Smolblog\Core\Media;

use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Denotes that a file has been added to a MediaHandler's store.
 */
readonly class MediaFileAdded extends DomainEvent {
	/**
	 * Construct the event.
	 *
	 * @param MediaFile              $mediaFile File being uploaded.
	 * @param Identifier             $userId    User uploading the media.
	 * @param Identifier|null        $id        ID of the event.
	 * @param DateTimeField|null $timestamp Timestamp of the event.
	 */
	public function __construct(
		public MediaFile $mediaFile,
		Identifier $userId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null
	) {
		parent::__construct(
			entityId: $mediaFile->id,
			userId: $userId,
			aggregateId: null,
			id: $id,
			timestamp: $timestamp,
		);
	}

	/**
	 * Get the payload for this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'handler' => $this->handler,
			'mimeType' => $this->mimeType,
			'details' => $this->details,
		];
	}
}
