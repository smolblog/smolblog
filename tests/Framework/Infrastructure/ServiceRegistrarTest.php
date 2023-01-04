<?php

namespace Smolblog\Framework\Infrastructure;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Exceptions\ServiceNotFoundException;
use Smolblog\Framework\Exceptions\ServiceRegistrarConfigurationException;

interface TestBasicInterface {}

final class TestBasicDependency implements TestBasicInterface {}

final class TestBasicService {
	public function __construct(public readonly TestBasicDependency $helper) {}
}

final class TestComplexService {
	public function __construct(
		public readonly TestBasicInterface $helper,
		public readonly TestBasicService $service,
		public readonly array $configuration = [],
	) {}
}

final class ServiceRegistrarTest extends TestCase {
	public function testItRegistersItself() {
		$container = new ServiceRegistrar([]);

		$this->assertTrue($container->has(ServiceRegistrar::class));
		$this->assertEquals($container, $container->get(ServiceRegistrar::class));
	}

	public function testItThrowsANotFoundExceptionWhenAServiceIsNotRegistered() {
		$this->expectException(ServiceNotFoundException::class);
		$container = new ServiceRegistrar([]);

		$this->assertFalse($container->has(self::class));
		$container->get(self::class);
	}

	public function testItThrowsAnExceptionWhenAServiceDoesNotExist() {
		$this->expectException(ServiceRegistrarConfigurationException::class);
		$wrongClass = __NAMESPACE__ . '\ClassNotExists';
		$container = new ServiceRegistrar([$wrongClass => []]);

		$this->assertTrue($container->has($wrongClass));
		$container->get($wrongClass);
	}

	public function testItThrowsAConfigurationExceptionWhenADependencyIsNotRegistered() {
		$this->expectException(ServiceRegistrarConfigurationException::class);
		$container = new ServiceRegistrar([TestBasicService::class => ['helper' => TestBasicDependency::class]]);

		$container->get(TestBasicService::class);
	}

	public function testAServiceWithNoDependenciesCanBeRegistered() {
		$container = new ServiceRegistrar([TestBasicDependency::class => []]);

		$this->assertTrue($container->has(TestBasicDependency::class));
		$this->assertInstanceOf(TestBasicDependency::class, $container->get(TestBasicDependency::class));
	}

	public function testAServiceWithServiceDependenciesCanBeRegistered() {
		$container = new ServiceRegistrar([
			TestBasicDependency::class => [],
			TestBasicService::class => ['helper' => TestBasicDependency::class],
		]);

		$this->assertTrue($container->has(TestBasicService::class));
		$actual = $container->get(TestBasicService::class);
		$this->assertInstanceOf(TestBasicService::class, $actual);
		$this->assertInstanceOf(TestBasicDependency::class, $actual->helper);
	}

	public function testAnInterfaceCanHaveAnImplementation() {
		$container = new ServiceRegistrar([
			TestBasicDependency::class => [],
			TestBasicInterface::class => TestBasicDependency::class,
		]);

		$this->assertTrue($container->has(TestBasicInterface::class));
		$this->assertInstanceOf(TestBasicDependency::class, $container->get(TestBasicInterface::class));
	}

	public function testAConfigCanBeACallable() {
		$container = new ServiceRegistrar([
			TestBasicDependency::class => fn() => new TestBasicDependency(),
		]);

		$this->assertTrue($container->has(TestBasicDependency::class));
		$this->assertInstanceOf(TestBasicDependency::class, $container->get(TestBasicDependency::class));
	}

	public function testADependencyCanComeFromACallable() {
		$container = new ServiceRegistrar([
			TestBasicService::class => ['helper' => fn() => new TestBasicDependency()],
		]);

		$this->assertTrue($container->has(TestBasicService::class));
		$actual = $container->get(TestBasicService::class);
		$this->assertInstanceOf(TestBasicService::class, $actual);
		$this->assertInstanceOf(TestBasicDependency::class, $actual->helper);
	}

	public function testAServiceWithComplexDependenciesCanBeRegistered() {
		$container = new ServiceRegistrar([
			TestBasicService::class => ['helper' => TestBasicDependency::class],
			TestBasicDependency::class => [],
			TestBasicInterface::class => TestBasicDependency::class,
			TestComplexService::class => [
				'helper' => TestBasicInterface::class,
				'service' => TestBasicService::class,
				'configuration' => fn() => ['camelot' => 'only a model'],
			]
		]);

		$this->assertTrue($container->has(TestComplexService::class));
		$actual = $container->get(TestComplexService::class);
		$this->assertInstanceOf(TestComplexService::class, $actual);
		$this->assertInstanceOf(TestBasicDependency::class, $actual->helper);
		$this->assertInstanceOf(TestBasicService::class, $actual->service);
		$this->assertEquals(['camelot' => 'only a model'], $actual->configuration);
	}

	public function testDependenciesCanBeNamedOutOfOrder() {
		$container = new ServiceRegistrar([
			TestBasicService::class => ['helper' => TestBasicDependency::class],
			TestBasicDependency::class => [],
			TestBasicInterface::class => TestBasicDependency::class,
			TestComplexService::class => [
				'configuration' => fn() => ['camelot' => 'only a model'],
				'helper' => TestBasicInterface::class,
				'service' => TestBasicService::class,
			]
		]);

		$this->assertTrue($container->has(TestComplexService::class));
		$actual = $container->get(TestComplexService::class);
		$this->assertInstanceOf(TestComplexService::class, $actual);
		$this->assertInstanceOf(TestBasicDependency::class, $actual->helper);
		$this->assertInstanceOf(TestBasicService::class, $actual->service);
	}

	public function testDependenciesCanBeInOrderWithoutNames() {
		$container = new ServiceRegistrar([
			TestBasicService::class => ['helper' => TestBasicDependency::class],
			TestBasicDependency::class => [],
			TestBasicInterface::class => TestBasicDependency::class,
			TestComplexService::class => [
				TestBasicInterface::class,
				TestBasicService::class,
				fn() => ['camelot' => 'only a model'],
			]
		]);

		$this->assertTrue($container->has(TestComplexService::class));
		$actual = $container->get(TestComplexService::class);
		$this->assertInstanceOf(TestComplexService::class, $actual);
		$this->assertInstanceOf(TestBasicDependency::class, $actual->helper);
		$this->assertInstanceOf(TestBasicService::class, $actual->service);
	}
}
