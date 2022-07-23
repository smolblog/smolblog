<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Model;
use Smolblog\Core\ModelHelper;

final class SocialAccountSiteLinkTestHelper implements ModelHelper {
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

final class SocialAccountSiteLinkTest extends TestCase {
	public function testAllDefinedFieldsCanBeAccessed() {
		$model = new SocialAccountSiteLink(withHelper: new SocialAccountSiteLinkTestHelper());

		$testData = [
			'site_id' => 5,
			'socialaccount_id' => 5,
			'additional_info' => 'plausible',
			'can_push' => true,
			'can_pull' => true,
		];

		foreach ($testData as $field => $value) {
			$model->$field = $value;
		}
		foreach ($testData as $field => $value) {
			$this->assertEquals($model->$field, $value);
		}
	}
}
