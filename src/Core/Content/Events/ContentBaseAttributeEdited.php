<?php

namespace Smolblog\Core\Content\Events;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Smolblog\Framework\Exceptions\InvalidMessageAttributesException;
use Smolblog\Framework\Exceptions\SmolblogException;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates a base attribute has been edited.
 *
 * A "base" attribute is an attribute on the BaseContent class, currently permalink and timestamp. These attributes
 * are common across all content types and are not extensions.
 */
class ContentBaseAttributeEdited extends ContentEvent {
	/**
	 * Relative URL for this content. Null indicates no change.
	 *
	 * @var string
	 */
	public readonly ?string $permalink;

	/**
	 * Date and time this content was first published. Null indicates no change.
	 *
	 * @var DateTimeInterface
	 */
	public readonly ?DateTimeInterface $contentTimestamp;

	/**
	 * Construct the event. Requires either $permalink or $contentTimestamp
	 *
	 * @throws InvalidMessageAttributesException Thrown if no updated attributes provided.
	 *
	 * @param string|null            $permalink        Updated permalink; null indicates no change.
	 * @param DateTimeInterface|null $contentTimestamp Date/Time content published; null indicates no change.
	 * @param Identifier             $contentId        Identifier for the content this event is about.
	 * @param Identifier             $userId           User responsible for this event.
	 * @param Identifier             $siteId           Site this content belongs to.
	 * @param Identifier|null        $id               Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp        Optional timestamp for this event.
	 */
	public function __construct(
		?string $permalink,
		?DateTimeInterface $contentTimestamp,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		if (!isset($permalink) && !isset($contentTimestamp)) {
			throw new InvalidMessageAttributesException(
				message: "ContentBaseAttributeEdited requires either permalink or contentTimestamp."
			);
		}

		$this->permalink = $permalink;
		$this->contentTimestamp = $contentTimestamp;
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get properties unique to this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'permalink' => $this->permalink,
			'contentTimestamp' => $this->contentTimestamp->format(DateTimeInterface::RFC3339_EXTENDED),
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
			'contentTimestamp' => self::safeDeserializeDate($payload['contentTimestamp']),
		];
	}
}
