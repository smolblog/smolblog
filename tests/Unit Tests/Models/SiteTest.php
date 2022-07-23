<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Model;
use Smolblog\Core\ModelHelper;

final class SiteTestHelper implements ModelHelper {
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

final class SiteTest extends TestCase {
	public function testAllDefinedFieldsCanBeAccessed() {
		$model = new Site(withHelper: new SiteTestHelper());

		$testData = [
			'id' => 12,
			'slug' => 'smolsnekworld',
			'title' => 'smolsnekworld',
			'url' => 'https://smolsnekworld.smol.blog',
		];

		foreach ($testData as $field => $value) {
			$model->$field = $value;
		}
		foreach ($testData as $field => $value) {
			$this->assertEquals($model->$field, $value);
		}
	}
}
