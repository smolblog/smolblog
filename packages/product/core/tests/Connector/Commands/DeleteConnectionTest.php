<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Test\TestCase;
use Smolblog\Core\Connector\Queries\ConnectionBelongsToUser;
use Smolblog\Foundation\Value\Fields\Identifier;

final class DeleteConnectionTest extends TestCase {
	public function testItIsAuthorizedByAConnectionBelongsToUserQuery() {
		$command = new DeleteConnection(connectionId: $this->randomId(), userId: $this->randomId());
		$authQuery = $command->getAuthorizationQuery();

		$this->assertInstanceOf(ConnectionBelongsToUser::class, $authQuery);
		$this->assertEquals($command->connectionId, $authQuery->connectionId);
		$this->assertEquals($command->userId, $authQuery->userId);
	}
}
