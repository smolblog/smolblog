<?php

namespace Smolblog\Core\ContentV1\Media;

use DateTimeInterface;
use Smolblog\Core\ContentV1\Events\ContentEvent;
use Smolblog\Framework\Objects\Identifier;

/**
 * Denotes that a file has been added to a MediaHandler's store.
 */
class MediaFileAdded extends ContentEvent {
	/**
	 * Construct the event.
	 *
	 * @param Identifier             $contentId ID of the Media object.
	 * @param Identifier             $userId    User uploading the media.
	 * @param Identifier             $siteId    Site media is being uploaded to.
	 * @param string                 $handler   Handler for this media.
	 * @param string|null            $mimeType  Media type (MIME) for the file. Optional.
	 * @param array                  $details   Handler-specific info for this media.
	 * @param Identifier|null        $id        ID of the event.
	 * @param DateTimeInterface|null $timestamp Timestamp of the event.
	 */
	public function __construct(
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		public readonly string $handler,
		public readonly ?string $mimeType,
		public readonly array $details,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
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
