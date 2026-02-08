<?php

namespace Smolblog\Core\Media\Extensions;

use Smolblog\Core\Content\Extensions\License\License;
use Smolblog\Core\Media\Entities\MediaExtensionConfiguration;
use Smolblog\Core\Media\Services\DefaultMediaExtensionService;

/**
 * Service for handling Licenses for Media.
 */
class LicenseService extends DefaultMediaExtensionService {
	/**
	 * Get the configuration for this extension.
	 *
	 * @return MediaExtensionConfiguration
	 */
	public static function getConfiguration(): MediaExtensionConfiguration {
		return new MediaExtensionConfiguration(
			key: 'license',
			displayName: 'License',
			extensionClass: License::class,
		);
	}
}
