<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Definitions\ModelHelper;
use Smolblog\Core\Exceptions\ModelException;
use Smolblog\Core\Models\BaseModel;

final class BaseModelTestHelper implements ModelHelper {
	public function getData(BaseModel $forModel = null, array $withProperties = []): ?array {
		if (empty($withProperties)) return null;

		return $withProperties;
	}

	public function save(BaseModel $model = null, array $withData = []): bool {
		return true;
	}
}

final class BaseModelTest extends TestCase {
	public function testItThrowsAnExceptionWhenNoHelperIsSupplied() {
		$this->expectException(ModelException::class);

		$model = new BaseModel();
	}

	public function testItCanBeInitializedWithNoData() {
		$model = new BaseModel(withHelper: new BaseModelTestHelper());

		$this->assertTrue($model->needsSave());
	}

	public function testItCanBeInitializedWithData() {
		$props = [
			'id' => 5,
			'name' => 'bob',
		];

		$model = new class(
			withHelper: new BaseModelTestHelper(),
			withData: $props
		) extends BaseModel {
			protected array $fields = ['id', 'name'];
		};

		$this->assertFalse($model->needsSave());

		foreach($props as $key => $value) {
			$this->assertEquals($model->$key, $value);
		}
	}

	public function testItsDataCanBeChangedAndSaved() {
		$model = new class(
			withHelper: new BaseModelTestHelper(),
			withData: [ 'id' => 3 ],
		) extends BaseModel {
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
}
