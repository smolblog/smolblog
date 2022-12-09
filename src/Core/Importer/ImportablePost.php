<?php

namespace Smolblog\Core\Importer;

use Smolblog\Framework\Value;

/**
 * Payload of a post that can be imported.
 */
readonly class ImportablePost extends Value {
	/**
	 * Create the object
	 *
	 * @param string $url      URL of the post to import.
	 * @param mixed  $postData Unparsed data from the service.
	 */
	public function __construct(
		public string $url,
		public mixed $postData
	) {
	}
}
