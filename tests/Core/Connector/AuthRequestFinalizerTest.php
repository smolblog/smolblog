<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Executor;

final class AuthRequestFinalizerTest extends TestCase {

	public function testItHandlesTheFinishAuthRequestCommand(): void {
		$connector = $this->createMock(Connector::class);
		$connector->expects($this->once())->method('createConnection')->willReturn(new Connection(
			userId: 5,
			provider: 'something',
			providerKey: 'something',
			displayName: 'something',
			details: ['something'=>'else'],
		));

		$connectors = $this->createStub(ConnectorRegistrar::class);
		$connectors->method('get')->willReturn($connector);

		$stateRepo = $this->createStub(AuthRequestStateReader::class);
		$stateRepo->method('get')->willReturn(new AuthRequestState(
			id: 'two',
			userId: 5,
			info: ['six' => 'eight'],
		));

		$connectionRepo = $this->createMock(ConnectionWriter::class);
		$connectionRepo->expects($this->once())->method('save');

		$commandBus = $this->createStub(Executor::class);
		$commandBus->expects($this->once())->method('exec');

		$service = new AuthRequestFinalizer(
			connectors: $connectors,
			stateRepo: $stateRepo,
			connectionRepo: $connectionRepo,
			commands: $commandBus,
		);

		$service->run(new FinishAuthRequest(
			provider: 'smol',
			stateKey: 'two',
			code: 'abc123',
		));
	}
}
