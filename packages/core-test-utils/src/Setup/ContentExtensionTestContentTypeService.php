<?php

namespace Smolblog\Core\Test\Setup;

use Smolblog\Core\Content\Entities\ContentTypeConfiguration;
use Smolblog\Core\Content\Services\DefaultContentTypeService;

final class ContentExtensionTestContentTypeService extends DefaultContentTypeService {
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			key: 'exttest',
			displayName: 'Extension Test',
			typeClass: ContentExtensionTestContentType::class,
		);
	}
}
