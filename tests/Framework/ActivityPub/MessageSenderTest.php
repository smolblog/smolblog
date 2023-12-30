<?php

namespace Smolblog\Framework\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Smolblog\Test\TestCase;

final class MessageSenderTest extends TestCase {
	protected function setUp(): void
	{
		$this->subject = new MessageSender(
			fetcher: $this->createStub(ClientInterface::class),
		);
	}

	public function testItCanBeInstantiatedWithOnlyAClient() {
		$basic = new MessageSender(
			fetcher: $this->createStub(ClientInterface::class),
		);

		$this->assertInstanceOf(MessageSender::class, $basic);
	}
}
