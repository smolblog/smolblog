<?php

namespace Smolblog\Core\Connection\Data;

use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Fields\Identifier;

class ConnectionDataService implements Service {
	public function connectionsForUser(Identifier $userId): array {
		return [];
	}
}
