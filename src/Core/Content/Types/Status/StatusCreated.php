<?php

namespace Smolblog\Core\Content\Types\Status;

use DateTimeInterface;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Core\Content\Markdown\NeedsMarkdownRendered;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a Status content has been created.
 */
class StatusCreated extends ContentCreated implements NeedsMarkdownRendered {
	use StatusEventKit;

	/**
	 * Create the Event.
	 *
	 * @throws InvalidContentException When contentType is not Static::class.
	 *
	 * @param string                 $text             Markdown-formatted text of the status.
	 * @param Identifier             $authorId         ID of the user that authored/owns this content.
	 * @param Identifier             $contentId        Identifier for the content this event is about.
	 * @param Identifier             $userId           User responsible for this event.
	 * @param Identifier             $siteId           Site this content belongs to.
	 * @param string|null            $permalink        Relative URL for this content.
	 * @param DateTimeInterface|null $publishTimestamp Date and time this content was first published.
	 * @param ContentVisibility|null $visibility       Visiblity of the content.
	 * @param Identifier|null        $id               Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp        Optional timestamp for this event.
	 * @param string|null            $contentType      For deserialization; must be Static::class if provided.
	 */
	public function __construct(
		public readonly string $text,
		Identifier $authorId,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?string $permalink = null,
		?DateTimeInterface $publishTimestamp = null,
		?ContentVisibility $visibility = null,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null,
		?string $contentType = null,
	) {
		if (isset($contentType) && $contentType !== Status::class) {
			// Something has gone very wrong somewhere!
			throw new InvalidContentException("StatusCreated initialized with non-Status content type: $contentType");
		}

		parent::__construct(
			contentType: Status::class,
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
	 * Get the payload as an array.
	 *
	 * @return array
	 */
	public function getContentPayload(): array {
		return ['text' => $this->text];
	}
}
