<?php

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

final readonly class TestMessage extends Value implements Message {
	use MessageKit;
	public function __construct(public string $message) { $this->meta = new MessageMetadata(); }
}

describe('MessageKit', function() {
	it('implements Message', fn() =>
		expect(new TestMessage('hello'))->toBeInstanceOf(Message::class)
	);

	it('will not serialize metadata by default', function() {
		$message = new readonly class('hello') extends Value implements Message, SerializableValue {
			use SerializableValueKit;
			use MessageKit;
			public function __construct(public string $message) { $this->meta = new MessageMetadata(); }
		};
		$message->setMetaValue('one', 'two');
		$message->setMetaValue('three', 'four');

		expect($message->toArray())->toEqual(['message' => 'hello']);
	});
});

describe('MessageKit::isPropogationStopped, ::stopMessage', function() {
	it('will stop propagation', function() {
		$message = new TestMessage('hello');
		expect($message->isPropagationStopped())->toBeFalse();

		$message->stopMessage();
		expect($message->isPropagationStopped())->toBeTrue();
	});
});

describe('MessageKit::getMetaValue, ::setMetaValue', function() {
	it('will get and set meta values', function() {
		$message = new TestMessage('hello');
		$message->setMetaValue('one', 'two');
		expect($message->getMetaValue('one'))->toBe('two');
	});
});
