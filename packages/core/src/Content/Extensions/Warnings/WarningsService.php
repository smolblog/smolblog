<?php

namespace Smolblog\Core\Content\Extensions\Warnings;

use Smolblog\Core\Content\Entities\ContentExtensionConfiguration;
use Smolblog\Core\Content\Services\DefaultContentExtensionService;

/**
 * Service for handling Warnings.
 */
class WarningsService extends DefaultContentExtensionService {
	/**
	 * Get the configuration for this extension.
	 *
	 * @return ContentExtensionConfiguration
	 */
	public static function getConfiguration(): ContentExtensionConfiguration {
		return new ContentExtensionConfiguration(
			key: 'warnings',
			displayName: 'Warnings',
			extensionClass: Warnings::class,
		);
	}
}
