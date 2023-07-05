<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Framework\Objects\Value;

/**
 * Get info back from the MediaHandler.
 */
class UploadedMediaInfo extends Value {
	/**
	 * Construct the info object.
	 *
	 * @param string    $handler Handler this is coming from.
	 * @param MediaType $type    Type of media this represents.
	 * @param array     $info    Handler-specific information.
	 */
	public function __construct(
		public readonly string $handler,
		public readonly MediaType $type,
		public readonly array $info
	) {
	}
}
