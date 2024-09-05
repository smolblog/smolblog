<?php

namespace Smolblog\Core\Connection\Commands;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Entities\AuthRequestState;
use Smolblog\Core\Connection\Entities\ConnectionInitData;
use Smolblog\Core\Connection\Events\ConnectionDeleted;
use Smolblog\Core\Model;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\ModelTest;

class DeleteConnectionTest extends ModelTest {
	const INCLUDED_MODELS = [Model::class];

	private ConnectionRepo & Stub $connections;

	protected function createMockServices(): array {
		$this->connections = $this->createStub(ConnectionRepo::class);

		return [
			ConnectionRepo::class => fn() => $this->connections,
		];
	}

	public function testHappyPath() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$connectionId = Identifier::fromString('267bef97-2fb9-4c76-b709-472578f46091');
		$command = new DeleteConnection(userId: $userId, connectionId: $connectionId);

		$this->connections->method('connectionBelongsToUser')->willReturn(true);

		$this->expectEvent(new ConnectionDeleted(
			entityId: $connectionId,
			userId: $userId,
		));

		$this->app->execute($command);
	}

	public function testNotAuthorized() {
		$this->expectException(CommandNotAuthorized::class);
		$this->connections->method('connectionBelongsToUser')->willReturn(false);

		$this->app->execute(new DeleteConnection(userId: $this->randomId(), connectionId: $this->randomId()));
	}
}
