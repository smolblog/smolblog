<?php

namespace Smolblog\Foundation\Value\Messages;

use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Foundation\Value;

/**
 * A command is a message that is sent to a service to perform an action.
 *
 * Commands are typically sent to a service to perform an action, such as creating a new user, or updating a user's
 * profile. Commands are also used to send events to the system, such as when a user creates a new post.
 */
abstract readonly class Command extends Value implements Message {
	use MessageKit;

	/**
	 * Initialize the command and its metadata.
	 */
	public function __construct() {
		$this->meta = new MessageMetadata();
	}
}
