<?php

namespace Smolblog\Test;

use Smolblog\Core\{Endpoint, EndpointRequest, EndpointResponse, Model, ModelHelper};
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
	protected $model;

	public function testItCanBeInitializedWithNoData() {
		$this->assertTrue($this->model->needsSave());
	}

	public function testAllDefinedFieldsCanBeAccessedAndSaved() {
		foreach ($this->model::FIELDS as $field => $type) {
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
			$this->model->$field = $sampleValue;
			$this->assertEquals($sampleValue, $this->model->$field);
		}
	}

	public function testUndefinedFieldsThrowAnError() {
		$this->expectNotice();

		$undefinedField = uniqid();

		$this->model->$undefinedField = 'nope';
	}
}

trait EndpointTestToolkit {
	protected $endpoint;

	public function testItCanBeInstantiated(): void {
		$this->assertInstanceOf(Endpoint::class, $this->endpoint);
	}

	public function testItCanBeCalled(): void {
		$response = $this->endpoint->run(new EndpointRequest());
		$this->assertInstanceOf(EndpointResponse::class, $response);
	}
}
