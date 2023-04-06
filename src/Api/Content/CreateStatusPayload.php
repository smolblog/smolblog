<?php

namespace Smolblog\Api\Content;

use Smolblog\Framework\Objects\Value;

/**
 * Body for the CreateStatus endpoint.
 */
class CreateStatusPayload extends Value {
	/**
	 * Construct the payload.
	 *
	 * @param string  $text    SFMD-formatted text of the status.
	 * @param boolean $publish True to publish immediately.
	 */
	public function __construct(
		public readonly string $text,
		public readonly bool $publish,
	) {
	}
}
