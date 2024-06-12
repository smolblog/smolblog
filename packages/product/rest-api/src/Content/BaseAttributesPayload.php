<?php

namespace Smolblog\Api\Content;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Payload for content base attributes.
 */
class BaseAttributesPayload extends Value {
	/**
	 * Construct the payload
	 *
	 * @param string|null            $permalink        Permalink slug for the content.
	 * @param DateTimeInterface|null $publishTimestamp Publish time for the content.
	 * @param Identifier|null        $authorId         ID of the content's author.
	 */
	public function __construct(
		public readonly ?string $permalink = null,
		public readonly ?DateTimeInterface $publishTimestamp = null,
		public readonly ?Identifier $authorId = null,
	) {
	}

	/**
	 * Serialize the payload.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$arr = parent::toArray();
		$arr['publishTimestamp'] = $this->publishTimestamp?->format(DateTimeInterface::RFC3339_EXTENDED);
		return $arr;
	}

	/**
	 * Deserialize the payload
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		return new BaseAttributesPayload(
			permalink: $data['permalink'] ?? null,
			publishTimestamp: self::safeDeserializeDate($data['publishTimestamp'] ?? ''),
			authorId: self::safeDeserializeIdentifier($data['authorId']),
		);
	}
}
