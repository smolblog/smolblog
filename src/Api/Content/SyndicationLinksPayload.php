<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\ParameterType;
use Smolblog\Framework\Objects\Value;

/**
 * Payload for updating syndication links.
 */
class SyndicationLinksPayload extends Value {
	/**
	 * Construct the payload
	 *
	 * @param string[] $add Links to add.
	 */
	public function __construct(
		#[ParameterType(type: 'array', required: true, items: 'string')] public readonly array $add = [],
	) {
	}
}
