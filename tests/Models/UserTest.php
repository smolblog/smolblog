<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Model;
use Smolblog\Core\ModelHelper;

final class UserTestHelper implements ModelHelper {
	public function findAll(string $forModelClass, array $withProperties = []): array {
		return [];
	}

	public function getData(Model $forModel = null, array $withProperties = []): ?array {
		if (empty($withProperties)) return null;
		return $withProperties;
	}

	public function save(Model $model = null, array $withData = []): bool {
		return true;
	}
}

final class UserTest extends TestCase {
	public function testAllDefinedFieldsCanBeAccessed() {
		$model = new User(withHelper: new UserTestHelper());

		$testData = [
			'id' => 6,
			'username' => 'bob',
			'email' => 'bob@bob.com',
		];

		foreach ($testData as $field => $value) {
			$model->$field = $value;
		}
		foreach ($testData as $field => $value) {
			$this->assertEquals($model->$field, $value);
		}
	}
}
