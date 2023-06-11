<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Exceptions\MessageNotAuthorizedException;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Message;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Messages\Query;

final class TestAuthorizableMessage extends Message implements AuthorizableMessage {
	public function getAuthorizationQuery(): Query {
		return new class() extends Query {};
	}
}

final class SecurityCheckServiceTest extends TestCase {
	public function testAnAuthorizedMessageWillProceed() {
		$bus = $this->createStub(MessageBus::class);
		$bus->method('fetch')->willReturn(true);

		$service = new SecurityCheckService(messageBus: $bus);
		$message = new TestAuthorizableMessage();

		$service->onAuthorizableMessage($message);
		$this->assertFalse($message->isPropagationStopped());
	}

	public function testAnUnauthorizedMessageWillStopAndThrowException() {
		$this->expectException(MessageNotAuthorizedException::class);

		$bus = $this->createStub(MessageBus::class);
		$bus->method('fetch')->willReturn(false);

		$service = new SecurityCheckService(messageBus: $bus);
		$message = new TestAuthorizableMessage();

		$service->onAuthorizableMessage($message);
		$this->assertTrue($message->isPropagationStopped());
	}
}
