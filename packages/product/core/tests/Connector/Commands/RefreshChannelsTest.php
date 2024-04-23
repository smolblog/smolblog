<?php

namespace Smolblog\Core\Connector\Commands;

use Smolblog\Test\TestCase;
use Smolblog\Core\Connector\Queries\ConnectionBelongsToUser;
use Smolblog\Framework\Objects\Identifier;

final class RefreshChannelsTest extends TestCase {
	public function testItIsAuthorizedByAConnectionBelongsToUserQuery() {
		$command = new RefreshChannels(connectionId: $this->randomId(), userId: $this->randomId());
		$authQuery = $command->getAuthorizationQuery();

		$this->assertInstanceOf(ConnectionBelongsToUser::class, $authQuery);
		$this->assertEquals($command->connectionId, $authQuery->connectionId);
		$this->assertEquals($command->userId, $authQuery->userId);
	}
}
