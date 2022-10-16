<?php

namespace Smolblog\Core\Importer;

/**
 * Payload of a post that can be imported.
 */
class ImportablePost {
	/**
	 * Create the object
	 *
	 * @param string $importKey String that uniquely identifies this post for this provider to determine if post has already been imported.
	 * @param mixed $paginationKey If this is the last post, information needed to fetch the next page.
	 * @param mixed $postData Unparsed data from the service.
	 */
	public function __construct(
		public readonly string $importKey,
		public readonly mixed $paginationKey,
		public readonly mixed $postData
	) {
	}
}
