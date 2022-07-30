<?php

namespace Smolblog\Test;

use Smolblog\Core\{Model, ModelHelper};

require_once __DIR__ . '/../vendor/autoload.php';

final class TestModelHelper implements ModelHelper {
	public function getData(Model $forModel = null, mixed $withId = null): ?array {
		if (is_array($withId)) { return $withId; }
		return ['id' => $withId];
	}

	public function save(Model $model = null, array $withData = []): bool {
		return true;
	}
}
