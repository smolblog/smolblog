<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use DateTimeInterface;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\Events\ContentCreated;
use Smolblog\Core\ContentV1\InvalidContentException;
use Smolblog\Core\ContentV1\Markdown\NeedsMarkdownRendered;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates a Note content has been created.
 */
readonly class NoteCreated extends ContentCreated implements NeedsMarkdownRendered {
	use NoteEventKit;

	/**
	 * Create the Event.
	 *
	 * @throws InvalidContentException When contentType is not Static::class.
	 *
	 * @param string                 $text             Markdown-formatted text of the note.
	 * @param Identifier             $authorId         ID of the user that authored/owns this content.
	 * @param Identifier             $contentId        Identifier for the content this event is about.
	 * @param Identifier             $userId           User responsible for this event.
	 * @param Identifier             $siteId           Site this content belongs to.
	 * @param DateTimeInterface|null $publishTimestamp Date and time this content was first published.
	 * @param Identifier|null        $id               Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp        Optional timestamp for this event.
	 */
	public function __construct(
		public readonly string $text,
		Identifier $authorId,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?DateTimeInterface $publishTimestamp = null,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null,
	) {
		parent::__construct(
			publishTimestamp: $publishTimestamp,
			authorId: $authorId,
			contentId: $contentId,
			userId: $userId,
			siteId: $siteId,
			id: $id,
			timestamp: $timestamp,
		);
	}

	/**
	 * Get the payload as an array.
	 *
	 * @return array
	 */
	public function getContentPayload(): array {
		return ['text' => $this->text];
	}

	/**
	 * Get the class of the content this event creates.
	 *
	 * @return string
	 */
	public function getContentType(): string {
		return 'note';
	}
}
