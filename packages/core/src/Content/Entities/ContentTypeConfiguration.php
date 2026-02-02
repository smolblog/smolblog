<?php

namespace Smolblog\Core\Content\Entities;

use Cavatappi\Foundation\Registry\RegisterableConfiguration;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

/**
 * Store configuration information for a Content Type.
 */
readonly class ContentTypeConfiguration implements Value, RegisterableConfiguration {
	use ValueKit;

	/**
	 * Create the configuration.
	 *
	 * @param string $key         Key for this content type.
	 * @param string $displayName User-friendly name for this content type.
	 * @param string $typeClass   PHP class implementing this type.
	 */
	public function __construct(
		public string $key,
		public string $displayName,
		public string $typeClass,
	) {}
}
