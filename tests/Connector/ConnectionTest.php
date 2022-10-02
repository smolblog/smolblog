<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase {
	public function testAnIdIsKnowableFromProviderAndKey() {
		$provider = 'woohoo';
		$key = 543;

		$connection = new Connection(
			userId: 5,
			provider: $provider,
			providerKey: $key,
			displayName: 'smol_bean',
			details: ['smol' => 'snek'],
		);

		$this->assertEquals($connection->id, Connection::buildId(provider: $provider, providerKey: $key));
	}
}
