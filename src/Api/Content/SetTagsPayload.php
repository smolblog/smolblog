<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\ParameterType;
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
		#[ParameterType(type: 'array', required: true, items: 'string')] public readonly array $tags
	) {
	}
}
