<?php

namespace Smolblog\Core\Content\Types;

use Smolblog\Core\Content\ContentTypeConfiguration;
use Smolblog\Core\Content\ContentTypeService;

abstract class BaseContentService implements ContentTypeService {
	abstract protected static function getTypeKey(): string;
	abstract protected static function getTypeClass(): string;

	public static function getConfiguration(): ContentTypeConfiguration {
		$fqcn = static::getTypeClass();
		$lastSlash = strrpos($fqcn, '\\');

		return new ContentTypeConfiguration(
			handle: static::getTypeKey(),
			displayName: $lastSlash > 0 ? substr($fqcn, $lastSlash + 1) : $fqcn,
			typeClass: $fqcn,
			singleItemQuery: 'tbd'
		);
	}
}
