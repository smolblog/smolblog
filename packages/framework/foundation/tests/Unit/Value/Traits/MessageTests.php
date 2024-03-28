<?php

use Smolblog\Framework\Foundation\Value;
use Smolblog\Framework\Foundation\Value\Traits\Message;
use Smolblog\Framework\Foundation\Value\Traits\MessageKit;
use Smolblog\Framework\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Framework\Foundation\Value\Traits\SerializableValue;
use Smolblog\Framework\Foundation\Value\Traits\SerializableValueKit;

test('MessageKit will implement Message', function(Message $message) {
	expect($message)->toHaveMethods(['stopMessage', 'getMetaValue', 'setMetaValue', 'isPropagationStopped']);
})->with('messages');

test('MessageKit will stop propagation', function(Message $message) {
	expect($message->isPropagationStopped())->toBeFalse();

	$message->stopMessage();
	expect($message->isPropagationStopped())->toBeTrue();
})->with('messages');

test('MessageKit will get and set meta values', function(Message $message) {
	$message->setMetaValue('one', 'two');
	expect($message->getMetaValue('one'))->toBe('two');
})->with('messages');

test('The message metadata will not serialize by default', function() {
	$message = new readonly class('hello') extends Value implements Message, SerializableValue {
		use SerializableValueKit;
		use MessageKit;
		public function __construct(public string $message) { $this->meta = new MessageMetadata(); }
	};
	$message->setMetaValue('one', 'two');
	$message->setMetaValue('three', 'four');

	expect($message->toArray())->toEqual(['message' => 'hello']);
});
