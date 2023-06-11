<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\ArrayType;
use Smolblog\Api\ParameterType;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Payload for updating syndication info.
 */
class SyndicationPayload extends Value {
	/**
	 * Construct the payload
	 *
	 * @param string[]     $add      Links to add.
	 * @param Identifier[] $channels Channels to syndicate to upon publish.
	 */
	public function __construct(
		#[ArrayType('string')] public readonly array $add = [],
		#[ArrayType(Identifier::class)] public readonly array $channels = [],
	) {
	}
}
