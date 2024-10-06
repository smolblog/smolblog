<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Core\Content\Entities\ContentExtensionConfiguration;
use Smolblog\Core\Content\Services\DefaultContentExtensionService;

/**
 * Service for handling Tags.
 */
class TagsService extends DefaultContentExtensionService {
	/**
	 * Get the configuration for this extension.
	 *
	 * @return ContentExtensionConfiguration
	 */
	public static function getConfiguration(): ContentExtensionConfiguration {
		return new ContentExtensionConfiguration(
			key: 'tags',
			displayName: 'Tags',
			extensionClass: Tags::class,
		);
	}
}
