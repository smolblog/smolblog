<?php

namespace Smolblog\Api\Connector;

use Smolblog\Test\TestCase;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\SuccessResponse;
use Smolblog\Core\Connector\Commands\DeleteConnection as DeleteConnectionCommand;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Test\EndpointTestToolkit;

final class DeleteConnectionTest extends TestCase {
	use EndpointTestToolkit;
	const ENDPOINT = DeleteConnection::class;

	public function testItGivesNotFoundIfTheConnectionIsInvalid() {
		$this->expectException(NotFound::class);

		$endpoint = new DeleteConnection($this->createStub(MessageBus::class));
		$endpoint->run(
			userId: $this->randomId(),
			params: ['id' => $this->randomId()],
			body: null,
		);
	}

	public function testItDeletesTheConnectionWithAllParameters() {
		$command = new DeleteConnectionCommand(
			connectionId: $this->randomId(),
			userId: $this->randomId(),
		);

		$bus = $this->mockBusExpects($command);
		$bus->method('fetch')->willReturn(true);

		$endpoint = new DeleteConnection($bus);
		$result = $endpoint->run(userId: $command->userId, params: ['id' => $command->connectionId]);

		$this->assertInstanceOf(SuccessResponse::class, $result);
	}
}
