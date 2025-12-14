<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\Entities\ContentTypeConfiguration;
use Smolblog\Core\Content\Services\DefaultContentTypeService;

/**
 * ContentTypeService to handle Pictures.
 */
class PictureService extends DefaultContentTypeService {
	/**
	 * Get the configuration for the Picture content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			key: Picture::KEY,
			displayName: 'Picture',
			typeClass: Picture::class,
		);
	}

	protected const CREATE_EVENT = PictureCreated::class;
	protected const UPDATE_EVENT = PictureUpdated::class;
	protected const DELETE_EVENT = PictureDeleted::class;
}
