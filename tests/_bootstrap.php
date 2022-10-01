<?php

namespace Smolblog\Test;

use Smolblog\Core\Endpoint\{Endpoint, EndpointRequest, EndpointResponse};
use Smolblog\Core\Model\{Model, ModelHelper, ModelField};
use Smolblog\Core\Post\PostStatus;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Model helper that returns provided data (success).
 */
final class TestModelHelper implements ModelHelper {
	public function getData(Model $forModel = null, mixed $withId = null): ?array {
		if (is_array($withId)) { return $withId; }
		return ['id' => $withId];
	}

	public function save(Model $model = null, array $withData = []): bool {
		return true;
	}
}

/**
 * Model helper that returns null (failure).
 */
final class TestModelEmptyHelper implements ModelHelper {
	public function getData(Model $forModel = null, mixed $withId = null): ?array {
		return null;
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

			if (is_a($type, ModelField::class)) {
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
			} elseif (class_exists($type)) {
				switch ($type) {
					// Add any enums that are used here since they are "final" classes
					case PostStatus::class:
						$sampleValue = PostStatus::Published;
						break;
					default:
						$sampleValue = $this->createStub($type);
						break;
				}
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
