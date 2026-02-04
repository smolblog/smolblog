<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\Entities\ContentTypeConfiguration;
use Smolblog\Core\Content\Services\DefaultContentTypeService;

/**
 * ContentTypeService to handle Reblogs.
 */
class ReblogService extends DefaultContentTypeService {
	/**
	 * Get the configuration for the Reblog content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			key: Reblog::getKey(),
			displayName: 'Reblog',
			typeClass: Reblog::class,
		);
	}

	protected const CREATE_EVENT = ReblogCreated::class;
	protected const UPDATE_EVENT = ReblogUpdated::class;
	protected const DELETE_EVENT = ReblogDeleted::class;
}
