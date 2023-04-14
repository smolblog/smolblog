<?php

namespace Smolblog\Core\Connector\Services;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Commands\DeleteConnection;
use Smolblog\Core\Connector\Events\ConnectionDeleted;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class ConnectionServiceTest extends TestCase {
	use EventComparisonTestKit;

	public function testItHandlesTheDeleteConnectionCommand() {
		$command = new DeleteConnection(
			userId: Identifier::createRandom(),
			connectionId: Identifier::createRandom(),
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
