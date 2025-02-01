<?php

namespace Smolblog\Foundation\Service\Registry;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Foundation\Value\Traits\ServiceConfiguration;
use Smolblog\Test\Kits\ServiceTestKit;
use Smolblog\Test\TestCase;
use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Exceptions\ServiceNotRegistered;

interface TestRegisterable extends Registerable {}
interface TestConfigurable extends ConfiguredRegisterable {}

class TestRegistry implements Registry {
	use RegistryKit;
	private static string $myInterface = TestRegisterable::class;
	public function __construct(ContainerInterface $container) { $this->container = $container; }
	public static function getInterfaceToRegister(): string { return self::$myInterface; }
	public static function _test_setInterface(string $int): void { self::$myInterface = $int; }
	public function _test_getLibrary(): array { return $this->library; }
}

#[CoversTrait(RegistryKit::class)]
final class RegistryKitTest extends TestCase {
	private array $basicServices;
	private array $configuredServices;
	private TestRegistry $service;
	private ContainerInterface & MockObject $container;

	protected function setUp(): void {
		$this->container = $this->createMock(ContainerInterface::class);
		$this->service = new TestRegistry($this->container);
	}

	protected function setUpBasic(): void {
		$this->basicServices = [
			new class() implements TestRegisterable { public static function getKey(): string { return 'one'; } },
			new class() implements TestRegisterable { public static function getKey(): string { return 'two'; } },
		];

		TestRegistry::_test_setInterface(TestRegisterable::class);
		$this->service->configure(array_map(fn($srv) => get_class($srv), $this->basicServices));
	}

	protected function setUpConfigured(): void {
		$this->configuredServices = [
			new class() implements TestConfigurable {
				public static function getConfiguration(): ServiceConfiguration {
					return new readonly class() implements ServiceConfiguration {
						public function getKey(): string { return 'one'; }
					};
				}
			},
			new class() implements TestConfigurable {
				public static function getConfiguration(): ServiceConfiguration {
					return new readonly class() implements ServiceConfiguration {
						public function getKey(): string { return 'two'; }
					};
				}
			},
		];

		TestRegistry::_test_setInterface(TestConfigurable::class);
		$this->service->configure(array_map(fn($srv) => get_class($srv), $this->configuredServices));
	}

	#[TestDox('::configure will configure the Registry for a Registerable interface')]
	function testConfigure() {
		$this->setUpBasic();

		$this->assertEquals(
			array_combine(
				keys: ['one', 'two'],
				values: array_map(fn($srv) => get_class($srv), $this->basicServices),
			),
			$this->service->_test_getLibrary()
		);
	}

	#[TestDox('::configure will configure the Registry for a ConfiguredRegisterable interface')]
	function testConfigureWithObjects() {
		$this->setUpConfigured();

		$this->assertEquals(
			array_combine(
				keys: ['one', 'two'],
				values: array_map(fn($srv) => get_class($srv), $this->configuredServices),
			),
			$this->service->_test_getLibrary()
		);
	}

	#[TestDox('::configure will throw an exception if the interface is not Registerable')]
	function testConfigureWithBadInterface() {
		$this->expectException(CodePathNotSupported::class);

		TestRegistry::_test_setInterface(Registry::class);
		$this->service->configure(['one' => self::class]);
	}

	#[TestDox('::has will return true if the given key is present and the class is in the container')]
	function testHasWithKeyAndContainer() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(true);

		$this->assertTrue($this->service->has('one'));
		$this->assertTrue($this->service->has('two'));
	}

	#[TestDox('::has will return false if the given key is not present')]
	function testHasWithNoKey() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(true);

		$this->assertFalse($this->service->has(get_class($this->basicServices[0])));
	}

	#[TestDox('::has will return false if the given key is present but the class is not in the container')]
	function testHasWithNoContainer() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(false);

		$this->assertFalse($this->service->has('one'));
	}

	#[TestDox('::get will return an instance of the class if the given key is present and the class is in the container')]
	function testGetWithContainerAndKey() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(true);
		$this->container->method('get')->willReturn('ServiceOne_class_instance');

		$this->assertEquals('ServiceOne_class_instance', $this->service->getService('one'));
	}

	#[TestDox('::get will throw exception if the given key is not present')]
	function testGetWithNoKey() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(true);
		$this->container->method('get')->willReturn('ServiceOne_class_instance');

		$this->expectException(ServiceNotRegistered::class);
		$this->service->getService(get_class($this->basicServices[0]));
	}

	#[TestDox('::get will throw exception if the given key is present but the class is not in the container')]
	function testGetWithNoContainer() {
		$this->setUpBasic();
		$this->container->method('has')->willReturn(false);
		$this->container->expects($this->never())->method('get');

		$this->expectException(ServiceNotRegistered::class);
		$this->service->getService('one');
	}

	#[TestDox('::getConfig will return the class config if the given key is present')]
	function testGetConfigWithKey() {
		$this->setUpConfigured();

		$config = $this->service->getConfig('one');
		$this->assertInstanceOf(ServiceConfiguration::class, $config);
		$this->assertEquals('one', $config->getKey());
	}

	#[TestDox('::getConfig will throw exception if the given key is not present')]
	function testGetConfigWithNoKey() {
		$this->setUpConfigured();

		$this->expectException(ServiceNotRegistered::class);
		$this->service->getService(get_class($this->configuredServices[0]));
	}
}

