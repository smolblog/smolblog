<?php

namespace Smolblog\Core\Site\Data;

use Smolblog\Core\Site\Entities\Site;
use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Fields\Identifier;

class SiteDataService implements Service {
	public function siteById(Identifier $siteId, Identifier $userId): ?Site {
		return null;
	}
}
