<?php

namespace Smolblog\Core\ContentV1\Events;

use DateTimeInterface;
use Smolblog\Framework\Exceptions\InvalidMessageAttributesException;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a base attribute has been edited.
 *
 * A "base" attribute is an attribute on the Content class, like permalinkSlug and timestamp. These attributes
 * are common across all content types and are not extensions.
 */
class ContentBaseAttributeEdited extends ContentEvent {
	/**
	 * Date and time this content was first published. Null indicates no change.
	 *
	 * @var DateTimeInterface
	 */
	public readonly ?DateTimeInterface $publishTimestamp;

	/**
	 * ID of the user that authored/owns this content.
	 *
	 * @var Identifier
	 */
	public readonly ?Identifier $authorId;

	/**
	 * Construct the event. Requires either $permalinkSlug or $publishTimestamp
	 *
	 * @throws InvalidMessageAttributesException Thrown if no updated attributes provided.
	 *
	 * @param Identifier             $contentId        Identifier for the content this event is about.
	 * @param Identifier             $userId           User responsible for this event.
	 * @param Identifier             $siteId           Site this content belongs to.
	 * @param DateTimeInterface|null $publishTimestamp Date/Time content published; null indicates no change.
	 * @param Identifier|null        $authorId         Identifier for the user that authored/owns this content.
	 * @param Identifier|null        $id               Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp        Optional timestamp for this event.
	 */
	public function __construct(
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?DateTimeInterface $publishTimestamp = null,
		?Identifier $authorId = null,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		if (!isset($publishTimestamp) && !isset($authorId)) {
			throw new InvalidMessageAttributesException(
				message: "ContentBaseAttributeEdited requires either authorId or publishTimestamp."
			);
		}

		$this->publishTimestamp = $publishTimestamp;
		$this->authorId = $authorId;
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get properties unique to this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'publishTimestamp' => $this->publishTimestamp?->format(DateTimeInterface::RFC3339_EXTENDED),
			'authorId' => $this->authorId?->toString(),
		];
	}

	/**
	 * Deserialize this event's payload.
	 *
	 * @param array $payload Serialized payload.
	 * @return array
	 */
	protected static function payloadFromArray(array $payload): array {
		return [
			...$payload,
			'publishTimestamp' => self::safeDeserializeDate($payload['publishTimestamp']),
			'authorId' => Identifier::fromString($payload['authorId']),
		];
	}
}
