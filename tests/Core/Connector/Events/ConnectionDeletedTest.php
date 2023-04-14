<?php

namespace Smolblog\Core\Connector\Events;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class ConnectionDeletedTest extends TestCase {
	public function testItHasNoPayload() {
		$event = new ConnectionDeleted(
			connectionId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
		);

		$this->assertEquals([], $event->getPayload());
	}
}
