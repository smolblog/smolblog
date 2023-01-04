<?php

namespace Smolblog\Framework\Infrastructure;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Exceptions\MessageNotAuthorizedException;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;

final class TestAuthorizableMessage implements AuthorizableMessage {
	use StoppableMessageKit;
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
