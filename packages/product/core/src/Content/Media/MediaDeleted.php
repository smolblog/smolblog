<?php

namespace Smolblog\Core\Content\Media;

use DateTimeInterface;
use Smolblog\Core\Content\Events\ContentEvent;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicate that attributes have been changed on a piece of media.
 */
class MediaDeleted extends ContentEvent {
	/**
	 * Construct the event.
	 *
	 * @param Identifier             $contentId ID of the Media object.
	 * @param Identifier             $userId    User uploading the media.
	 * @param Identifier             $siteId    Site media is being uploaded to.
	 * @param Identifier|null        $id        ID of the event.
	 * @param DateTimeInterface|null $timestamp Timestamp of the event.
	 */
	public function __construct(
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
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
		return [];
	}
}
