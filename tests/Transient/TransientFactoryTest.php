<?php

namespace Smolblog\Core\Factories;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\{Model, ModelHelper};

final class TestTransientModelHelper implements ModelHelper {
	private $store = [];

	public function getData(Model $forModel = null, mixed $withId = null): ?array {
		$result = $this->store[$withId] ?? ['key' => $withId];
		return $result;
	}

	public function save(Model $model = null, array $withData = []): bool {
		$this->store[$withData['key']] = $withData;
		return true;
	}
}

final class TransientFactoryTest extends TestCase {
	public function testATransientCanBeSetAndRetrieved() {
		$factory = new TransientFactory(new TestTransientModelHelper());
		$factory->setTransient('camelot', 'only a model');

		$this->assertEquals('only a model', $factory->getTransient('camelot'));
	}

	public function testATransientCanExpire() {
		$factory = new TransientFactory(new TestTransientModelHelper());
		$factory->setTransient('camelot', 'only a model', -1);

		$this->assertNull($factory->getTransient('camelot'));
	}
}
