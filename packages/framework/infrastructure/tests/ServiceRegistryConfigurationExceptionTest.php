<?php

namespace Smolblog\Framework\Infrastructure;

use Exception;
use Smolblog\Foundation\Service\KeypairGenerator;
use Smolblog\Test\TestCase;

final class ServiceRegistryConfigurationExceptionTest extends TestCase {
	public function testItUsesTheSuppliedMessageIfOneIsGiven() {
		$ex = new ServiceRegistryConfigurationException(
			service: KeypairGenerator::class,
			config: [],
			message: 'Something happened.'
		);

		$this->assertEquals('Something happened.', $ex->getMessage());
	}

	public function testItGeneratesADefaultMessageIfNeeded() {
		$ex = new ServiceRegistryConfigurationException(
			service: KeypairGenerator::class,
			config: [],
		);

		$this->assertEquals('Configuration error for '.KeypairGenerator::class.' in ServiceRegistry.', $ex->getMessage());
	}

	public function testItGeneratesADefaultMessageUsingThePreviousException() {
		$ex = new ServiceRegistryConfigurationException(
			service: KeypairGenerator::class,
			config: [],
			previous: new Exception('There was a problem.')
		);

		$this->assertEquals(
			'Configuration error for '.KeypairGenerator::class.' in ServiceRegistry: There was a problem.',
			$ex->getMessage()
		);
	}
}
