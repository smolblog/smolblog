<?php

namespace Smolblog\Core\Registrar;

use PHPUnit\Framework\TestCase;

class RegisterableMock implements Registerable {
	public static function config(): object { return (object)['slug' => 'one', 'data' => 'two']; }
	public readonly string $id;

	public function __construct() {
		$this->id = uniqid();
	}
}

class ConcreteRegistrar extends Registrar {
	protected function processConfig(mixed $config): string {
		return $config->slug;
	}
}

final class RegistrarTest extends TestCase {
	public function testRegisterableObjectCanBeRegisteredAndRetrieved() {
		$expected = new RegisterableMock();

		$registrar = new ConcreteRegistrar();
		$registrar->register(class: RegisterableMock::class, factory: fn() => $expected);

		$this->assertTrue($registrar->has('one'));

		$actual = $registrar->get('one');
		$this->assertEquals(
			$expected->id,
			$actual->id
		);
	}

	public function testThrowsRegistrationExceptionWhenClassNotRegisterable() {
		$this->expectException(RegistrationException::class);

		$registrar = new ConcreteRegistrar();
		$registrar->register(class: RegistrarTest::class, factory: fn() => false);
	}

	public function testRegistrarGivesNullWhenNotFound() {
		$registrar = new ConcreteRegistrar();
		$this->assertFalse($registrar->has('nope'));
		$this->assertNull($registrar->get('nope'));
	}
}
