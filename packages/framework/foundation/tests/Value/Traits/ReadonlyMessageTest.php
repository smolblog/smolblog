<?php

namespace Smolblog\Foundation\Value\Traits;

use PHPUnit\Framework\Attributes\CoversTrait;
use Smolblog\Foundation\Value;
use Smolblog\Test\TestCase;

final readonly class ExampleReadonlyMessage extends Value implements Message {
	use ReadonlyMessageKit;
	public function __construct(public string $message) { $this->meta = new MessageMetadata(); }
}

#[CoversTrait(ReadonlyMessageKit::class)]
#[CoversClass(MessageMetadata::class)]
final class ReadonlyMessageTest extends TestCase {
	public function itWillNotSerializeMetadataByDefault() {
		$message = new readonly class('hello') extends Value implements Message, SerializableValue {
			use SerializableValueKit;
			use MessageKit;
			public function __construct(public readonly string $message) { $this->meta = new MessageMetadata(); }
		};
		$message->setMetaValue('one', 'two');
		$message->setMetaValue('three', 'four');

		$this->assertEquals(['message' => 'hello'], $message->serializeValue());
	}

	public function testItWillStopPropagation() {
		$message = new ExampleReadonlyMessage('hello');
		$this->assertFalse($message->isPropagationStopped());

		$message->stopMessage();
		$this->assertTrue($message->isPropagationStopped());
	}

	public function testItWillGetAndSetMetaValues() {
		$message = new ExampleReadonlyMessage('hello');
		$message->setMetaValue('one', 'two');
		$this->assertEquals('two', $message->getMetaValue('one'));
	}
}
