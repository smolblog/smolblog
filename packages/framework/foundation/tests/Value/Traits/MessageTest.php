<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;
use Smolblog\Test\TestCase;

final readonly class ExampleMessage extends Value implements Message {
	use MessageKit;
	public function __construct(public string $message) { $this->meta = new MessageMetadata(); }
}

#[CoversTrait(MessageKit::class)]
#[CoversClass(MessageMetadata::class)]
final class MessageTest extends TestCase {
	public function itWillNotSerializeMetadataByDefault() {
		$message = new readonly class('hello') extends Value implements Message, SerializableValue {
			use SerializableValueKit;
			use MessageKit;
			public function __construct(public string $message) { $this->meta = new MessageMetadata(); }
		};
		$message->setMetaValue('one', 'two');
		$message->setMetaValue('three', 'four');

		$this->assertEquals(['message' => 'hello'], $message->serializeValue());
	}

	public function testItWillStopPropagation() {
		$message = new ExampleMessage('hello');
		$this->assertFalse($message->isPropagationStopped());

		$message->stopMessage();
		$this->assertTrue($message->isPropagationStopped());
	}

	public function testItWillGetAndSetMetaValues() {
		$message = new ExampleMessage('hello');
		$message->setMetaValue('one', 'two');
		$this->assertEquals('two', $message->getMetaValue('one'));
	}
}
