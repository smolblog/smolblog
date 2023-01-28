<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeInterface;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a Status content has been created.
 */
class StatusCreated extends ContentCreated {
	/**
	 * Internal Status object to assist with processing.
	 *
	 * @var InternalStatusBody
	 */
	private InternalStatusBody $internal;

	/**
	 * Create the Event.
	 *
	 * @param string                 $text             Markdown-formatted text of the status.
	 * @param string                 $permalink        Relative URL for this content.
	 * @param DateTimeInterface      $publishTimestamp Date and time this content was first published.
	 * @param ContentVisibility      $visibility       Visiblity of the content.
	 * @param Identifier             $authorId         ID of the user that authored/owns this content.
	 * @param Identifier             $contentId        Identifier for the content this event is about.
	 * @param Identifier             $userId           User responsible for this event.
	 * @param Identifier             $siteId           Site this content belongs to.
	 * @param Identifier|null        $id               Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp        Optional timestamp for this event.
	 */
	public function __construct(
		public readonly string $text,
		string $permalink,
		DateTimeInterface $publishTimestamp,
		ContentVisibility $visibility,
		Identifier $authorId,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		$this->internal = new InternalStatusBody(text: $text);
		parent::__construct(
			permalink: $permalink,
			publishTimestamp: $publishTimestamp,
			visibility: $visibility,
			authorId: $authorId,
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
			id: $id,
			timestamp: $timestamp,
		);
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
