<?php

namespace Smolblog\Api\Content;

use DateTimeInterface;
use Smolblog\Framework\Objects\Identifier;
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
}
