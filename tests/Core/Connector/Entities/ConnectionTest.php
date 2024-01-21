<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\Kits\SerializableTestKit;

final class ConnectionTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void {
		$this->subject = new Connection(
			userId: $this->randomId(true),
			provider: 'test',
			providerKey: '12345',
			displayName: 'Test Account',
			details: ['one' => 'two'],
		);
	}

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
