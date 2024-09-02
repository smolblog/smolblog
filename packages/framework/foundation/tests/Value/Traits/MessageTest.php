<?php

use PHPUnit\Framework\Attributes\CoversTrait;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;
use Smolblog\Test\TestCase;

final class ExampleMessage implements Message {
	use MessageKit;
	public function __construct(public readonly string $message) {}
}

#[CoversTrait(MessageKit::class)]
final class MessageTest extends TestCase {
	public function itWillNotSerializeMetadataByDefault() {
		$message = new class('hello') implements Message, SerializableValue {
			use SerializableValueKit;
			use MessageKit;
			public function __construct(public readonly string $message) {}
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
