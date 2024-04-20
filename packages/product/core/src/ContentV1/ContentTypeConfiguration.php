<?php

namespace Smolblog\Core\ContentV1;

use Smolblog\Framework\Objects\Value;

/**
 * Store configuration information for a Content Type.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class ContentTypeConfiguration extends Value {
	/**
	 * Construct the configuration.
	 *
	 * @param string      $handle            Unique URL-friendly name for the content type.
	 * @param string      $displayName       Human-readable name for the content type.
	 * @param string      $typeClass         Fully-qualified class name of the content type.
	 * @param string      $singleItemQuery   Class name of the "by ID" query.
	 * @param string|null $deleteItemCommand Command to delete an item.
	 */
	public function __construct(
		public readonly string $handle,
		public readonly string $displayName,
		public readonly string $typeClass,
		public readonly ?string $singleItemQuery = null,
		public readonly ?string $deleteItemCommand = null,
	) {
	}
}
