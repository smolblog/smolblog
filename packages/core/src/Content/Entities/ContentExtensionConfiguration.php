<?php

namespace Smolblog\Core\Content\Entities;

use Cavatappi\Foundation\Registry\RegisterableConfiguration;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

/**
 * Store configuration information for a Content Extension.
 */
class ContentExtensionConfiguration implements Value, RegisterableConfiguration {
	use ValueKit;

	/**
	 * Construct the configuration.
	 *
	 * @param string $key            Unique URL-friendly name for the content extension.
	 * @param string $displayName    Human-readable name for the content extension.
	 * @param string $extensionClass Fully-qualified class name of the content extension.
	 */
	public function __construct(
		public readonly string $key,
		public readonly string $displayName,
		public readonly string $extensionClass,
	) {}
}
