<?php

namespace Smolblog\Core\Content\Data;

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Fields\Identifier;

class ContentDataService implements Service {
	public function contentList(Identifier $siteId, Identifier $userId): array {
		return [];
	}

	public function contentById(Identifier $contentId, Identifier $userId): ?Content {
		return null;
	}
}
