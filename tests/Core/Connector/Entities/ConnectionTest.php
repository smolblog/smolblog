<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class ConnectionTest extends TestCase {
	public function testAnIdIsKnowableFromProviderAndKey() {
		$provider = 'woohoo';
		$key = 543;

		$connection = new Connection(
			userId: $this->randomId(),
			provider: $provider,
			providerKey: $key,
			displayName: 'smol_bean',
			details: ['smol' => 'snek'],
		);

		$this->assertEquals($connection->id, Connection::buildId(provider: $provider, providerKey: $key));
	}
}
