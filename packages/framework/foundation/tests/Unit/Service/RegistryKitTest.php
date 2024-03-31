<?php

namespace Smolblog\Foundation\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Test\Kits\ServiceTestKit;
use Smolblog\Test\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

final class TestRegistry implements Registry {
	use RegistryKit;
	public function __construct(ContainerInterface $container) { $this->container = $container; }
	public static function getInterfaceToRegister(): string { return 'Interface'; }
	private function getKeyForClass(string $class): string { return $class . '_key'; }
	public function getLibrary(): array { return $this->library; }
}

#[CoversClass(RegistryKit::class)]
final class RegistryKitTest extends TestCase {
	use ServiceTestKit;

	protected function setUp(): void {
		$this->service = $this->setUpService(TestRegistry::class);
		$this->service->configure(['ServiceOne', 'ServiceTwo']);
	}

	#[TestDox('::configure will configure the Registry')]
	function testConfigure() {
		$this->assertEquals(
			['ServiceOne_key' => 'ServiceOne', 'ServiceTwo_key' => 'ServiceTwo'],
			$this->service->getLibrary()
		);
	}

	#[TestDox('::has will return true if the given key is present and the class is in the container')]
	function testHasWithKeyAndContainer() {
		$this->deps->container->method('has')->willReturn(true);

		$this->assertTrue($this->service->has('ServiceOne_key'));
	}

	#[TestDox('::has will return false if the given key is not present')]
	function testHasWithNoKey() {
		$this->deps->container->method('has')->willReturn(true);

		$this->assertFalse($this->service->has('ServiceOne'));
	}

	#[TestDox('::has will return false if the given key is present but the class is not in the container')]
	function testHasWithNoContainer() {
		$this->deps->container->method('has')->willReturn(false);

		$this->assertFalse($this->service->has('ServiceOne_key'));
	}

	#[TestDox('::get will return an instance of the class if the given key is present and the class is in the container')]
	function testGetWithContainerAndKey() {
		$this->deps->container->method('has')->willReturn(true);
		$this->deps->container->method('get')->willReturn('ServiceOne_class_instance');

		$this->assertEquals('ServiceOne_class_instance', $this->service->get('ServiceOne_key'));
	}

	#[TestDox('::get will return null if the given key is not present')]
	function testGetWithNoKey() {
		$this->deps->container->method('has')->willReturn(true);
		$this->deps->container->method('get')->willReturn('ServiceOne_class_instance');

		$this->assertNull($this->service->get('ServiceOne'));
	}

	#[TestDox('::get will return false if the given key is present but the class is not in the container')]
	function testGetWithNoContainer() {
		$this->deps->container->method('has')->willReturn(false);
		$this->deps->container->expects($this->never())->method('get');

		$this->assertNull($this->service->get('ServiceOne_key'));
	}
}

