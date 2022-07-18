<?php

namespace Smolblog\Core\EndpointParameters;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector;
use Smolblog\Core\Registrars\ConnectorRegistrar;

final class ConnectorSlugTest extends TestCase {
	public function testItAcceptsAKnownSlug(): void {
		$connector = $this->getMockForAbstractClass(Connector::class);
		$connector->expects($this->any())
		          ->method('slug')
		          ->will($this->returnValue('camelot'));
		ConnectorRegistrar::register($connector);

		$parameter = new ConnectorSlug();
		$this->assertTrue($parameter->validate($connector->slug()));
	}

	public function testItRejectsAnUnknownSlug(): void {
		$parameter = new ConnectorSlug();
		$this->assertFalse($parameter->validate('nope'));
	}
}
