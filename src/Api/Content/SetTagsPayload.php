<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\ArrayType;
use Smolblog\Framework\Objects\Value;

/**
 * Payload for setting tags on content.
 */
class SetTagsPayload extends Value {
	/**
	 * Create the payload.
	 *
	 * @param string[] $tags Tags to set on the content.
	 */
	public function __construct(
		#[ArrayType('string')] public readonly array $tags
	) {
	}
}
