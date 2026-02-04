<?php

namespace Smolblog\Core\Content\Types\Article;

use Smolblog\Core\Content\Entities\ContentTypeConfiguration;
use Smolblog\Core\Content\Services\DefaultContentTypeService;

/**
 * ContentTypeService to handle Articles.
 */
class ArticleService extends DefaultContentTypeService {
	/**
	 * Get the configuration for the Article content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			key: Article::getKey(),
			displayName: 'Article',
			typeClass: Article::class,
		);
	}
}
