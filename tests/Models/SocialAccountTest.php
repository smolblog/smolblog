<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Environment;
use Smolblog\Core\Model;
use Smolblog\Core\ModelHelper;
use Smolblog\Core\Exceptions\ModelException;

final class SocialAccountTestHelper implements ModelHelper {
	public function findAll(string $forModelClass, array $withProperties = []): array {
		return [
			new $forModelClass(withHelper: $this, withData: ['id' => 1, ...$withProperties]),
			new $forModelClass(withHelper: $this, withData: ['id' => 2, ...$withProperties]),
			new $forModelClass(withHelper: $this, withData: ['id' => 3, ...$withProperties]),
		];
	}

	public function getData(Model $forModel = null, array $withProperties = []): ?array {
		if (empty($withProperties)) return null;
		return $withProperties;
	}

	public function save(Model $model = null, array $withData = []): bool {
		return true;
	}
}

final class SocialAccountTestEnvironment extends Environment {
	public function getHelperForModel(string $modelClass): ModelHelper {
		return new SocialAccountTestHelper();
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

	public function testUndefinedFieldsThrowAnError() {
		$this->expectNotice();
		$model = new SocialAccount(withHelper: new SocialAccountTestHelper());

		$model->undefinedField = 'nope';
	}

	public function testStaticFactoryMethodsReturnSocialAccountModels() {
		Environment::bootstrap(new SocialAccountTestEnvironment());

		$this->assertInstanceOf(SocialAccount::class, SocialAccount::create());

		$multiple = SocialAccount::find(['prop' => 'erty']);
		$this->assertIsArray($multiple);
		foreach($multiple as $single) {
			$this->assertInstanceOf(SocialAccount::class, $single);
		}
	}
}
