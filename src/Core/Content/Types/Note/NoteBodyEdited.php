<?php

namespace Smolblog\Core\Content\Types\Note;

use DateTimeInterface;
use Smolblog\Core\Content\Events\ContentBodyEdited;
use Smolblog\Core\Content\Markdown\NeedsMarkdownRendered;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a note' text has been edited.
 */
class NoteBodyEdited extends ContentBodyEdited implements NeedsMarkdownRendered {
	use NoteEventKit;

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
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
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
