<?php

namespace Smolblog\Core\Content\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\ServiceConfiguration;
use Smolblog\Foundation\Value\Traits\ServiceConfigurationKit;

/**
 * Store configuration information for a Content Extension.
 */
readonly class ContentExtensionConfiguration extends Value implements ServiceConfiguration {
	use ServiceConfigurationKit;

	/**
	 * Construct the configuration.
	 *
	 * @param string $key            Unique URL-friendly name for the content extension.
	 * @param string $displayName    Human-readable name for the content extension.
	 * @param string $extensionClass Fully-qualified class name of the content extension.
	 */
	public function __construct(
		string $key,
		public readonly string $displayName,
		public readonly string $extensionClass,
	) {
		$this->key = $key;
	}
}
