<?php

namespace Smolblog\Core\Registrars;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Endpoint;

/** @backupStaticAttributes enabled */
final class EndpointRegistrarTest extends TestCase {
	public function testEndpointCanBeRegisteredAndRetrieved() {
		$expected = $this->getMockBuilder(Endpoint::class)->setConstructorArgs([])->getMock();

		EndpointRegistrar::register(endpoint: $expected, withSlug: 'camelot');
		$actual = EndpointRegistrar::retrieve('camelot');

		$this->assertEquals(
			$expected->route,
			$actual->route
		);
	}

	public function testRegistrarGivesNullWhenNotFound() {
		$this->assertNull(EndpointRegistrar::retrieve('nope'));
	}
}
