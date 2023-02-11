<?php

namespace Smolblog\Core\Content\Events;

use DateTimeInterface;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates an event where a new piece of Content has been created.
 */
abstract class ContentCreated extends ContentEvent {
	/**
	 * Create the event
	 *
	 * @throws InvalidContentException Thrown if an invalid state is given.
	 *
	 * @param Identifier             $authorId         ID of the user that authored/owns this content.
	 * @param Identifier             $contentId        Identifier for the content this event is about.
	 * @param Identifier             $userId           User responsible for this event.
	 * @param Identifier             $siteId           Site this content belongs to.
	 * @param string                 $permalink        Relative URL for this content.
	 * @param DateTimeInterface      $publishTimestamp Date and time this content was first published.
	 * @param ContentVisibility      $visibility       Visiblity of the content.
	 * @param Identifier|null        $id               Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp        Optional timestamp for this event.
	 */
	public function __construct(
		public readonly Identifier $authorId,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		public readonly ?string $permalink = null,
		public readonly ?DateTimeInterface $publishTimestamp = null,
		public readonly ?ContentVisibility $visibility = null,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		if (
			$this->visibility === ContentVisibility::Published &&
			(!isset($this->permalink) || !isset($this->publishTimestamp))
		) {
			throw new InvalidContentException('Permalink and timestamp are required if content is published.');
		}

		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get the content type this event is creating (usually the fully-qualified class name).
	 *
	 * @return string
	 */
	abstract public function getContentType(): string;

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

	/**
	 * Get the specific payload from the child class.
	 *
	 * @return array
	 */
	abstract protected function getContentPayload(): array;

	/**
	 * Get the payload for serialization.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'authorId' => $this->authorId->toString(),
			'permalink' => $this->permalink ?? null,
			'publishTimestamp' => $this->publishTimestamp?->format(DateTimeInterface::RFC3339_EXTENDED),
			'visibility' => $this->visibility?->value,
			...$this->getContentPayload(),
		];
	}

	/**
	 * Deserialize the specific payload for the child class.
	 *
	 * @param array $payload Array to deserialize.
	 * @return array
	 */
	protected static function contentPayloadFromArray(array $payload): array {
		return $payload;
	}

	/**
	 * Deserialize the array for this event.
	 *
	 * @param array $payload Array to deserialize.
	 * @return array
	 */
	protected static function payloadFromArray(array $payload): array {
		$contentPayload = array_diff_key(
			$payload,
			array_flip(['authorId', 'permalink', 'publishTimestamp', 'visibility'])
		);

		return [
			'authorId' => self::safeDeserializeIdentifier($payload['authorId'] ?? null),
			'permalink' => $payload['permalink'] ?? null,
			'publishTimestamp' => self::safeDeserializeDate($payload['publishTimestamp'] ?? ''),
			'visibility' => ContentVisibility::tryFrom($payload['visibility'] ?? ''),
			...static::contentPayloadFromArray($contentPayload),
		];
	}
}
