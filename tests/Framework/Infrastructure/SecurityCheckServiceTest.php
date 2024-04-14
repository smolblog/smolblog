<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Exceptions\MessageNotAuthorizedException;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Messages\Query;

final readonly class TestAuthorizableMessage extends Value implements Message, AuthorizableMessage {
	use MessageKit;

	public function getAuthorizationQuery(): Query {
		return new readonly class() extends Query {};
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
