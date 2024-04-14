<?php

namespace Smolblog\Core\ContentV1\Extensions\Tags;

use DateTimeInterface;
use Smolblog\Core\ContentV1\ContentExtension;
use Smolblog\Core\ContentV1\Events\ContentExtensionEdited;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates the tags have been set on a particular piece of content.
 */
readonly class TagsSet extends ContentExtensionEdited {
	/**
	 * Store the processed tags for use by other projections.
	 *
	 * @var Tags
	 */
	private Tags $tags;

	/**
	 * Construct the event
	 *
	 * @param string[]               $tagText   Array of unprocessed tag input.
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		public readonly array $tagText,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		$this->tags = new Tags(tags: array_map(fn($text) => new Tag(text: $text), $this->tagText));
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get the processed tags.
	 *
	 * @return Tags
	 */
	public function getNewExtension(): Tags {
		return $this->tags;
	}

	/**
	 * Serialize this event's unique fields.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return ['tagText' => $this->tagText];
	}
}
