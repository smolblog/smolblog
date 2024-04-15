<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Test\TestCase;
use Smolblog\Foundation\Exceptions\MessageNotAuthorized;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Messages\Query;

final readonly class TestAuthorizableMessage extends Value implements Message, AuthorizableMessage {
	use MessageKit;

	public function __construct() { $this->meta = new MessageMetadata(); }

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
		$this->expectException(MessageNotAuthorized::class);

		$bus = $this->createStub(MessageBus::class);
		$bus->method('fetch')->willReturn(false);

		$service = new SecurityCheckService(messageBus: $bus);
		$message = new TestAuthorizableMessage();

		$service->onAuthorizableMessage($message);
		$this->assertTrue($message->isPropagationStopped());
	}
}
