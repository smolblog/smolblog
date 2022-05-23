<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Environment;
use Smolblog\Core\Model;
use Smolblog\Core\ModelHelper;
use Smolblog\Core\Exceptions\ModelException;

final class SocialAccountSiteLinkTestHelper implements ModelHelper {
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

final class SocialAccountSiteLinkTestEnvironment extends Environment {
	public function getHelperForModel(string $modelClass): ModelHelper {
		return new SocialAccountSiteLinkTestHelper();
	}
}

/** @backupStaticAttributes enabled */
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

	public function testUndefinedFieldsThrowAnError() {
		$this->expectNotice();
		$model = new SocialAccountSiteLink(withHelper: new SocialAccountSiteLinkTestHelper());

		$model->undefinedField = 'nope';
	}

	public function testStaticFactoryMethodsReturnSocialAccountSiteLinkModels() {
		Environment::bootstrap(new SocialAccountSiteLinkTestEnvironment());

		$this->assertInstanceOf(SocialAccountSiteLink::class, SocialAccountSiteLink::create());

		$multiple = SocialAccountSiteLink::find(['prop' => 'erty']);
		$this->assertIsArray($multiple);
		foreach($multiple as $single) {
			$this->assertInstanceOf(SocialAccountSiteLink::class, $single);
		}
	}
}
