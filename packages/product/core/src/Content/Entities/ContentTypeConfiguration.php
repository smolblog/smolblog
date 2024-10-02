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

	public function __construct(
		string $key,
		public string $displayName,
		public string $typeClass,
	) {
		$this->key = $key;
	}
}
