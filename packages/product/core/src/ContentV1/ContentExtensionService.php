<?php

namespace Smolblog\Core\ContentV1;

/**
 * Denotes a service for a particular content extension.
 *
 * Mostly exists to be auto-discovered and added to the registry.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
interface ContentExtensionService {
	/**
	 * Get the configuration for this content extension.
	 *
	 * @return ContentExtensionConfiguration
	 */
	public static function getConfiguration(): ContentExtensionConfiguration;
}
