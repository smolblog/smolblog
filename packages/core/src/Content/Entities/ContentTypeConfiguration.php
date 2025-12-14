<?php

namespace Smolblog\Core\Content\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\ServiceConfiguration;
use Smolblog\Foundation\Value\Traits\ServiceConfigurationKit;

/**
 * Store configuration information for a Content Type.
 */
readonly class ContentTypeConfiguration extends Value implements ServiceConfiguration {
	use ServiceConfigurationKit;

	/**
	 * Create the configuration.
	 *
	 * @param string $key         Key for this content type.
	 * @param string $displayName User-friendly name for this content type.
	 * @param string $typeClass   PHP class implementing this type.
	 */
	public function __construct(
		string $key,
		public string $displayName,
		public string $typeClass,
	) {
		$this->key = $key;
	}
}
