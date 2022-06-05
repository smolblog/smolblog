<?php

namespace Smolblog\Core;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Definitions\Endpoint;
use Smolblog\Core\Exceptions\EnvironmentException;

/** @backupStaticAttributes enabled */
final class EnvironmentTest extends TestCase {
	public function testItThrowsAnExceptionWhenItIsNotBootstrapped(): void {
		$this->expectException(EnvironmentException::class);

		Environment::get();
	}

	public function testItThrowsAnExceptionWhenItIsBootstrappedTwice(): void {
		$this->expectException(EnvironmentException::class);

		Environment::bootstrap(new Environment());
		Environment::bootstrap(new Environment());
	}

	public function testItCanBeAccessedAfterBeingBootstrapped(): void {
		Environment::bootstrap(new Environment());

		$this->assertInstanceOf(Environment::class, Environment::get());
	}

	public function testItThrowsAnExceptionWhenRegisterEndpointIsNotImplemented(): void {
		$this->expectException(EnvironmentException::class);

		$stubEndpoint = $this->getMockBuilder(Endpoint::class)->getMock();

		Environment::bootstrap(new Environment());
		Environment::get()->registerEndpoint($stubEndpoint);
	}

	public function testItThrowsAnExceptionWhenGetHelperForModelIsNotImplemented(): void {
		$this->expectException(EnvironmentException::class);

		Environment::bootstrap(new Environment());
		Environment::get()->getHelperForModel('Model\\Class');
	}

	public function testItThrowsAnExceptionWhenSetTransientIsNotImplemented(): void {
		$this->expectException(EnvironmentException::class);

		Environment::bootstrap(new Environment());
		Environment::get()->setTransient(
			name: 'tempTransient',
			value: 'tempValue',
			secondsUntilExpiration: 1,
		);
	}

	public function testItThrowsAnExceptionWhenGetTransientValueIsNotImplemented(): void {
		$this->expectException(EnvironmentException::class);

		Environment::bootstrap(new Environment());
		Environment::get()->getTransientValue('tempTransient');
	}
}
