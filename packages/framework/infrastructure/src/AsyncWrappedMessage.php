<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Framework\Messages\Message as DeprecatedMessage;

/**
 * Wrapper to designate a message that should be enqueued to handle on a different thread.
 *
 * @deprecated Use Smolblog\Foundation\Value\Jobs\Job
 */
class AsyncWrappedMessage extends DeprecatedMessage {
	/**
	 * Construct the message wrapper.
	 *
	 * @param DeprecatedMessage|Message $message DeprecatedMessage to enqueue.
	 */
	public function __construct(
		public readonly DeprecatedMessage|Message $message
	) {
	}
}
