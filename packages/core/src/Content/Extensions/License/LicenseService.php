<?php

namespace Smolblog\Core\Content\Extensions\License;

use Smolblog\Core\Content\Entities\ContentExtensionConfiguration;
use Smolblog\Core\Content\Services\DefaultContentExtensionService;

/**
 * Service for handling Licenses for Content.
 */
class LicenseService extends DefaultContentExtensionService {
	/**
	 * Get the configuration for this extension.
	 *
	 * @return ContentExtensionConfiguration
	 */
	public static function getConfiguration(): ContentExtensionConfiguration {
		return new ContentExtensionConfiguration(
			key: 'license',
			displayName: 'License',
			extensionClass: License::class,
		);
	}
}
