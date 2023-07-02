<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Core\Content\ContentTypeConfiguration;
use Smolblog\Core\Content\ContentTypeService;

class MediaService implements ContentTypeService {
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			handle: 'media',
			displayName: 'Media',
			typeClass: Media::class,
			singleItemQuery: '',
		);
	}
}
