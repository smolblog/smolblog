<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Foundation\Value\Traits\MessageMetadata;

/**
 * Wrapper to designate a message that should be enqueued to handle on a different thread.
 */
readonly class AsyncWrappedMessage extends Value implements Message {
	use MessageKit;

	/**
	 * Construct the message wrapper.
	 *
	 * @param Message $message Message to enqueue.
	 */
	public function __construct(
		public readonly Message $message
	) {
		$this->meta = new MessageMetadata();
	}
}
