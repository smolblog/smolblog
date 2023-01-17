<?php

namespace Smolblog\Test;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\ConnectionEstablished;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Mock\App;

final class ConnectorTest extends TestCase {
	public function testAConnectorEventWillPersist() {
		$connectionAdded = new ConnectionEstablished(
			provider: 'smolblog',
			providerKey: 'woohoo543',
			displayName: 'snek.smol.blog',
			details: [ 'token' => '14me24you' ],
			userId: Identifier::fromString('f7d1d707-bcf1-46bf-94d5-0c7942d51ca3')
		);

		App::dispatch($connectionAdded);

		$result = App::fetch(new ConnectionById(
			Connection::buildId(provider: 'smolblog', providerKey: 'woohoo543')
		));

		$this->assertInstanceOf(Connection::class, $result);
	}
}
