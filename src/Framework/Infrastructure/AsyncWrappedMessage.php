<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Framework\Messages\Message;

/**
 * Wrapper to designate a message that should be enqueued to handle on a different thread.
 */
class AsyncWrappedMessage extends Message {
	/**
	 * Construct the message wrapper.
	 *
	 * @param Message $message Message to enqueue.
	 */
	public function __construct(
		public readonly Message $message
	) {
	}
}
