<?php

namespace Smolblog\Core\Connector\Events;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class ConnectionDeletedTest extends TestCase {
	public function testItHasNoPayload() {
		$event = new ConnectionDeleted(
			connectionId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertEquals([], $event->getPayload());
	}
}
