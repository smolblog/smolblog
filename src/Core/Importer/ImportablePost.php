<?php

namespace Smolblog\Core\Importer;

use Smolblog\Framework\Objects\Value;

/**
 * Payload of a post that can be imported.
 */
class ImportablePost extends Value {
	/**
	 * Create the object
	 *
	 * @param string $url      URL of the post to import.
	 * @param mixed  $postData Unparsed data from the service.
	 */
	public function __construct(
		public readonly string $url,
		public readonly mixed $postData
	) {
	}
}
