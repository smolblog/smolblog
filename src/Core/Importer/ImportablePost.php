<?php

namespace Smolblog\Core\Importer;

use Smolblog\Framework\Value;

/**
 * Payload of a post that can be imported.
 */
class ImportablePost extends Value {
	/**
	 * Create the object
	 *
	 * @param string $importKey String that uniquely identifies this post for this provider to determine if post has
	 *                          already been imported.
	 * @param mixed  $postData  Unparsed data from the service.
	 */
	public function __construct(
		public readonly string $importKey,
		public readonly mixed $postData
	) {
	}
}
