<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Test\TestCase;
use Smolblog\Core\Connector\Commands\DeleteConnection;
use Smolblog\Core\Connector\Events\ConnectionDeleted;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\Kits\EventComparisonTestKit;

final class ConnectionServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheDeleteConnectionCommand() {
		$command = new DeleteConnection(
			userId: $this->randomId(),
			connectionId: $this->randomId(),
		);

		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo(
			new ConnectionDeleted(
				connectionId: $command->connectionId,
				userId: $command->userId,
			)
		));

		$service = new ConnectionService(bus: $bus);
		$service->onDeleteConnection($command);
	}
}
