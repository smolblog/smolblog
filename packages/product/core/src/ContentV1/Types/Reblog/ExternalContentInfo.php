<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Framework\Objects\Value;

/**
 * Object containing info about an external embeddable URL.
 */
class ExternalContentInfo extends Value {
	/**
	 * Construct the object
	 *
	 * @param string $title Title of the external content.
	 * @param string $embed HTML code to embed the external content.
	 */
	public function __construct(
		public readonly string $title,
		public readonly string $embed,
	) {
	}
}
