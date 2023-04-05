<?php

namespace Smolblog\Api\Content;

use Smolblog\Framework\Objects\Value;

/**
 * Schema for reblog endpoints.
 */
class BaseReblogPayload extends Value {
	/**
	 * Construct the payload
	 *
	 * @param string|null $url     URL being reblogged.
	 * @param string|null $comment Optional SFMD-formatted comment.
	 */
	public function __construct(
		public readonly ?string $url = null,
		public readonly ?string $comment = null,
	) {
	}
}
