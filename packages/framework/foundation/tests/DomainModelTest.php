<?php

namespace Smolblog\Foundation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Test\TestCase;

final class DomainModelTestNoConstructor {}
final class DomainModelTestNoDependencies { public function __construct() {} }
final class DomainModelTestSomeDependencies {
	public function __construct(private CommandBus $bus, private EventDispatcherInterface $event) {}
}

final class DomainModelTestNoType { public function __construct(private $whatAmI) {} }
final class DomainModelTestUnionType { public function __construct(private CommandBus|Registry $thing) {} }
final class DomainModelTestIntersectionType { public function __construct(private CommandBus & Registry $also) {} }
final class DomainModelTestBuiltInType { public function __construct(private string $noGood) {} }


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

	public function testItCanAutomaticallyLearnDependenciesForBasicServices() {
		$model = new class() extends DomainModel {
			public const AUTO_SERVICES = [
				DomainModelTestNoConstructor::class,
				DomainModelTestNoDependencies::class,
				DomainModelTestSomeDependencies::class,
			];
		};

		$expected = [
			DomainModelTestNoConstructor::class => [],
			DomainModelTestNoDependencies::class => [],
			DomainModelTestSomeDependencies::class => [
				'bus' => CommandBus::class,
				'event' => EventDispatcherInterface::class,
			],
		];

		$this->assertEquals($expected, get_class($model)::getDependencyMap());
	}

	public static function badServices(): array {
		return [
			'no' => [new class() extends DomainModel {
				const AUTO_SERVICES = [DomainModelTestNoType::class];
			}],
			'a union' => [new class() extends DomainModel {
				const AUTO_SERVICES = [DomainModelTestUnionType::class];
			}],
			'an intersection' => [new class() extends DomainModel {
				const AUTO_SERVICES = [DomainModelTestIntersectionType::class];
			}],
			'a built-in' => [new class() extends DomainModel {
				const AUTO_SERVICES = [DomainModelTestBuiltInType::class];
			}],
		];
	}

	#[DataProvider('badServices')]
	#[TestDox('It will not automatically create a map if a dependency has $_dataName type.')]
	public function testBadServices(DomainModel $model) {
		$this->expectException(CodePathNotSupported::class);
		get_class($model)::getDependencyMap();
	}
}
