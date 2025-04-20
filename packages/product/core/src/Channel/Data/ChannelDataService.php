<?php

namespace Smolblog\Core\Channel\Data;

use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Fields\Identifier;

class ContentDataService implements Service {
	public function channelsForSite(Identifier $siteId, Identifier $userId): array {
		return [];
	}

	public function availableChannels(Identifier $userId): array {
		return [];
	}
}
