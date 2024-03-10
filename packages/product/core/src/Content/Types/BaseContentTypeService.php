<?php

namespace Smolblog\Core\Content\Types;

use Smolblog\Core\Content\ContentTypeConfiguration;
use Smolblog\Core\Content\ContentTypeService;

/**
 * Base class with sane defaults for most ContentTypeServices.
 */
abstract class BaseContentTypeService implements ContentTypeService {
	abstract protected static function getTypeKey(): string;
	abstract protected static function getTypeClass(): string;

	/**
	 * Get the configuration for this content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration {
		$fqcn = static::getTypeClass();
		$lastSlash = strrpos($fqcn, '\\');

		return new ContentTypeConfiguration(
			handle: static::getTypeKey(),
			displayName: $lastSlash > 0 ? substr($fqcn, $lastSlash + 1) : $fqcn,
			typeClass: $fqcn,
		);
	}
}
