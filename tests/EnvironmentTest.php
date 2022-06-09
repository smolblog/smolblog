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

	// https://stackoverflow.com/questions/9296529/phpunit-how-to-test-if-callback-gets-called
	public function testItCanBeGivenACallbackBeforeBootstrapping(): void {
		$called = false;
		Environment::addBootstrapCallback(function() use (&$called) {
			$called = true;
		});

		$this->assertFalse($called, 'Callback should not be called yet.');

		Environment::bootstrap(new Environment());

		$this->assertTrue($called, 'Callback should be called');
	}

	public function testItWillRunACallbackImmediatelyIfAlreadyBootstrapped(): void {
		Environment::bootstrap(new Environment());

		$called = false;
		Environment::addBootstrapCallback(function() use (&$called) {
			$called = true;
		});

		$this->assertTrue($called, 'Callback should be called');
	}
}
