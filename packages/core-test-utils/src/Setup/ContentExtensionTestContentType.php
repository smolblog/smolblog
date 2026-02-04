<?php

namespace Smolblog\Core\Test\Setup;

use Cavatappi\Foundation\Value\ValueKit;
use Smolblog\Core\Content\Entities\ContentType;

final class ContentExtensionTestContentType implements ContentType {
	use ValueKit;

	public static function getKey(): string {
		return 'exttest';
	}
	public function __construct(public readonly string $title) {}
}
