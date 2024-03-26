<?php

use Smolblog\Framework\Foundation\Value\Traits\Message;
use Smolblog\Framework\Foundation\Value\Traits\MessageKit;
use Smolblog\Framework\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Framework\Foundation\Value\Messages\Command;
use Smolblog\Framework\Foundation\Value\Messages\DomainEvent;
use Smolblog\Framework\Foundation\Value\Messages\Query;
use Smolblog\Framework\Foundation\Value;
use Smolblog\Framework\Foundation\Value\Fields\DateTime;
use Smolblog\Framework\Foundation\Value\Fields\RandomIdentifier;

/**
 * Objects that use MessageKit and should have that functionality tested.
 */
dataset('messages', [
	'basic test' => new readonly class('hello') extends Value implements Message {
		use MessageKit;
		public function __construct(public string $message) { $this->meta = new MessageMetadata(); }
	},
	'command' => new readonly class() extends Command { public function __construct() { parent::__construct(); } },
	'query' => new readonly class() extends Query { public function __construct() { parent::__construct(); } },
	'event' => new readonly class() extends DomainEvent {
		public function __construct() {
			parent::__construct(
				id: new RandomIdentifier(),
				timestamp: new DateTime(),
				userId: new RandomIdentifier(),
				aggregateId: new RandomIdentifier(),
				entityId: new RandomIdentifier(),
			);
		}
	},
]);
