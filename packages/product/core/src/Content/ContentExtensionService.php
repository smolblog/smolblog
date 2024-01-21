<?php

namespace Smolblog\Core\Content;

/**
 * Denotes a service for a particular content extension.
 *
 * Mostly exists to be auto-discovered and added to the registry.
 */
interface ContentExtensionService {
	/**
	 * Get the configuration for this content extension.
	 *
	 * @return ContentExtensionConfiguration
	 */
	public static function getConfiguration(): ContentExtensionConfiguration;
}
