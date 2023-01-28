<?php

namespace Smolblog\Core\Content\Events;

use DateTimeInterface;
use Smolblog\Core\Content\BaseContent;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates an event where a new piece of Content has been created.
 */
abstract class ContentCreated extends ContentEvent {
	/**
	 * Undocumented function
	 *
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
		public readonly string $permalink,
		public readonly DateTimeInterface $publishTimestamp,
		public readonly ContentVisibility $visibility,
		public readonly Identifier $authorId,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get the HTML-formatted body text.
	 *
	 * @return array
	 */
	abstract public function getNewBody(): string;

	/**
	 * Get the title.
	 *
	 * @return array
	 */
	abstract public function getNewTitle(): string;
}
