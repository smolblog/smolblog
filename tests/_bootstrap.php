<?php

namespace Smolblog\Test;

use Smolblog\Core\{Model, ModelHelper};
use Smolblog\Core\Definitions\ModelField;

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

trait ModelTestToolkit {
	private abstract function createModel();

	public function testItCanBeInitializedWithNoData() {
		$model = $this->createModel();

		$this->assertTrue($model->needsSave());
	}

	public function testAllDefinedFieldsCanBeAccessedAndSaved() {
		$model = $this->createModel();

		foreach ($model::FIELDS as $field => $type) {
			$sampleValue = '';
			switch ($type) {
				case ModelField::int:
					$sampleValue = 543;
					break;
				case ModelField::float:
					$sampleValue = 5.43;
					break;
				case ModelField::string:
					$sampleValue = 'Fhqwhgads';
					break;
			}
			$model->$field = $sampleValue;
			$this->assertEquals($sampleValue, $model->$field);
		}
	}

	public function testUndefinedFieldsThrowAnError() {
		$this->expectNotice();
		$model = $this->createModel();

		$undefinedField = uniqid();

		$model->$undefinedField = 'nope';
	}
}
