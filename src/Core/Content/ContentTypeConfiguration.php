<?php

namespace Smolblog\Core\Content;

use Smolblog\Framework\Objects\Value;

/**
 * Store configuration information for a Content Type.
 */
class ContentTypeConfiguration extends Value {
	/**
	 * Construct the configuration.
	 *
	 * @param string $handle          Unique URL-friendly name for the content type.
	 * @param string $displayName     Human-readable name for the content type.
	 * @param string $typeClass       Fully-qualified class name of the content type.
	 * @param string $singleItemQuery Class name of the "by ID" query.
	 */
	public function __construct(
		public readonly string $handle,
		public readonly string $displayName,
		public readonly string $typeClass,
		public readonly string $singleItemQuery,
	) {
	}
}
