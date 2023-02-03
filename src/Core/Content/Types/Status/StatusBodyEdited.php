<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeInterface;
use Smolblog\Core\Content\Events\ContentBodyEdited;
use Smolblog\Core\Content\Events\ContentEvent;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a status' text has been edited.
 */
class StatusBodyEdited extends ContentBodyEdited {
	/**
	 * Create the event.
	 *
	 * @param string                 $text      Updated text.
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		public readonly string $text,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		$this->internal = new InternalStatusBody(text: $text);
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get a title-appropriate truncation of the content.
	 *
	 * @return string
	 */
	public function getNewTitle(): string {
		return $this->internal->getTruncated(100);
	}

	/**
	 * Get the HTML-formatted content of the status.
	 *
	 * @return string
	 */
	public function getNewBody(): string {
		return $this->internal->text;
	}

	/**
	 * Get the payload as an array.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return ['text' => $this->text];
	}
}
