<?php

namespace Smolblog\Core;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Exceptions\ModelException;

final class ModelTestHelper implements ModelHelper {
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

final class ModelTestEnvironment extends Environment {
	public function getHelperForModel(string $modelClass): ModelHelper {
		return new ModelTestHelper();
	}
}

final class ModelTestChild extends Model {}

/** @backupStaticAttributes enabled */
final class ModelTest extends TestCase {
	public function testItThrowsAnExceptionWhenNoHelperIsSupplied() {
		$this->expectException(ModelException::class);

		$model = new Model();
	}

	public function testItCanBeInitializedWithNoData() {
		$model = new Model(withHelper: new ModelTestHelper());

		$this->assertTrue($model->needsSave());
	}

	public function testItCanBeInitializedWithData() {
		$props = [
			'id' => 5,
			'name' => 'bob',
		];

		$model = new class(
			withHelper: new ModelTestHelper(),
			withData: $props
		) extends Model {
			protected array $fields = ['id', 'name'];
		};

		$this->assertFalse($model->needsSave());

		foreach($props as $key => $value) {
			$this->assertEquals($model->$key, $value);
		}
	}

	public function testItsDataCanBeChangedAndSaved() {
		$model = new class(
			withHelper: new ModelTestHelper(),
			withData: [ 'id' => 3 ],
		) extends Model {
			protected array $fields = ['id', 'name'];
		};

		$this->assertFalse($model->needsSave());
		$this->assertEquals($model->id, 3);

		$model->id = 5;
		$model->name = 'bob';

		$this->assertEquals($model->id, 5);
		$this->assertEquals($model->name, 'bob');
		$this->assertTrue($model->needsSave());

		$model->save();
		$this->assertFalse($model->needsSave());
	}

	public function testUndefinedFieldsThrowAnError() {
		$this->expectNotice();
		$model = new Model(withHelper: new ModelTestHelper());

		$model->undefinedField = 'nope';
	}

	public function testStaticCreateMethodReturnsCallingModel() {
		Environment::bootstrap(new ModelTestEnvironment());

		$this->assertInstanceOf(ModelTestChild::class, ModelTestChild::create());
	}

	public function testStaticFindMethodReturnsArrayOfCallingModels() {
		Environment::bootstrap(new ModelTestEnvironment());

		$multiple = ModelTestChild::find(['prop' => 'erty']);
		$this->assertIsArray($multiple);
		foreach($multiple as $single) {
			$this->assertInstanceOf(ModelTestChild::class, $single);
		}
	}
}
