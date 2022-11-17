<?php

namespace Smolblog\App\Registrars;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smolblog\App\Registrars\RegistrationException;

interface MockRegisterable {}

class RegisterableMock implements MockRegisterable {
	public readonly string $id;

	public function __construct() {
		$this->id = uniqid();
	}
}

class ConcreteRegistrar extends GenericRegistrar {
	protected string $interface = MockRegisterable::class;
}

final class GenericRegistrarTest extends TestCase {
	public function testRegisterableObjectCanBeRegisteredAndRetrieved() {
		$expected = new RegisterableMock();

		$container = $this->createStub(ContainerInterface::class);
		$container->method('has')->willReturn(true);
		$container->method('get')->willReturn($expected);

		$registrar = new ConcreteRegistrar(container: $container);
		$registrar->register(key: 'one', class: RegisterableMock::class);

		$this->assertTrue($registrar->has('one'));

		$actual = $registrar->get('one');
		$this->assertEquals(
			$expected->id,
			$actual->id
		);
	}

	public function testThrowsRegistrationExceptionWhenClassNotRegisterable() {
		$this->expectException(RegistrationException::class);

		$registrar = new ConcreteRegistrar(container: $this->createStub(ContainerInterface::class));
		$registrar->register(key: 'nope', class: GenericRegistrarTest::class);
	}

	public function testRegistrarGivesNullWhenNotRegistered() {
		$registrar = new ConcreteRegistrar(container: $this->createStub(ContainerInterface::class));

		$this->assertFalse($registrar->has('nope'));
		$this->assertNull($registrar->get('nope'));
	}

	public function testRegistrarGivesNullWhenNotInContainer() {
		$container = $this->createStub(ContainerInterface::class);
		$container->method('has')->willReturn(false);

		$registrar = new ConcreteRegistrar($container);
		$registrar->register(key: 'maybe', class: RegisterableMock::class);

		$this->assertFalse($registrar->has('maybe'));
		$this->assertNull($registrar->get('maybe'));
	}
}
