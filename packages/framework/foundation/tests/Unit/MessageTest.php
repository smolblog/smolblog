<?php
use Smolblog\Framework\Foundation\Message;
use Smolblog\Framework\Foundation\MessageKit;
use Smolblog\Framework\Foundation\MessageMetadata;
use Smolblog\Framework\Foundation\Value;

readonly class TestMessage extends Value implements Message {
	use MessageKit;
	public function __construct(public string $message) { $this->meta = new MessageMetadata(); }
}

test('MessageKit will implement Message', function() {
	$message = new TestMessage('hello');
	expect($message)->toBeInstanceOf(TestMessage::class);
	expect($message)->toHaveMethods(['stopMessage', 'getMetaValue', 'setMetaValue', 'isPropagationStopped']);
});

test('MessageKit will stop propagation', function() {
	$message = new TestMessage('hello');
	expect($message->isPropagationStopped())->toBeFalse();

	$message->stopMessage();
	expect($message->isPropagationStopped())->toBeTrue();
});

test('MessageKit will get and set meta values', function() {
	$message = new TestMessage('hello');
	$message->setMetaValue('one', 'two');
	expect($message->getMetaValue('one'))->toBe('two');
});

test('The message meatadata will not serialize by default', function() {
	$message = new TestMessage('hello');
	$message->setMetaValue('one', 'two');
	$message->setMetaValue('three', 'four');

	expect($message->serialize())->toEqual(['message' => 'hello']);
});
