<?php

namespace Smolblog\Api\Content;

use Smolblog\Framework\Objects\Value;

/**
 * Payload for editing Media attributes.
 */
class EditMediaPayload extends Value {
	/**
	 * Construct the payload.
	 *
	 * @param string $title             Title for the image.
	 * @param string $accessibilityText Alt text for the image.
	 */
	public function __construct(
		public readonly string $title,
		public readonly string $accessibilityText,
	) {
	}
}
