<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Model;
use Smolblog\Core\ModelHelper;

final class SocialAccountTestHelper implements ModelHelper {
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

final class SocialAccountTest extends TestCase {
	public function testAllDefinedFieldsCanBeAccessed() {
		$model = new SocialAccount(withHelper: new SocialAccountTestHelper());

		$testData = [
			'id' => 5,
			'user_id' => 3,
			'social_type' => 'smolblog',
			'social_username' => 'ronyo',
			'oauth_token' => '1234567890abcdef',
			'oauth_secret' => '1234567890abcdef',
		];

		foreach ($testData as $field => $value) {
			$model->$field = $value;
		}
		foreach ($testData as $field => $value) {
			$this->assertEquals($model->$field, $value);
		}
	}
}
