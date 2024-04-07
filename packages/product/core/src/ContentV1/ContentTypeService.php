<?php

namespace Smolblog\Core\ContentV1;

/**
 * Denotes a service for a particular content type.
 *
 * Mostly exists to be auto-discovered and added to the registry.
 */
interface ContentTypeService {
	/**
	 * Get the configuration for this content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration;
}
