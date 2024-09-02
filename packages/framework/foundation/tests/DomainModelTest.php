<?php

namespace Smolblog\Foundation;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Test\TestCase;

#[CoversClass(DomainModel::class)]
final class DomainModelTest extends TestCase {
	public function testItProvidesTheServicesConstantByDefault() {
		$model = new class() extends DomainModel {
			public const SERVICES = ['one', 'two'];
		};

		$this->assertEquals(['one', 'two'], get_class($model)::getDependencyMap());
	}

	public function testItCanBeOverriddenWithRuntimeValues() {
		$model = new class() extends DomainModel {
			public const SERVICES = ['one', 'two'];
			public static function getDependencyMap(): array { return ['three', ['four']]; }
		};

		$this->assertEquals(['three', ['four']], get_class($model)::getDependencyMap());
	}
}
