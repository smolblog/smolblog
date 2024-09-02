<?php

namespace Smolblog\Foundation\Value\Messages;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Foundation\Value\Traits\ReadonlyMessageKit;

/**
 * A command is a message that is sent to a service to perform an action.
 *
 * Commands are typically sent to a service to perform an action, such as creating a new user, or updating a user's
 * profile. Commands are also used to send events to the system, such as when a user creates a new post.
 *
 * Not `readonly` to allow middleware/plugins to modify parts of the Command. For example, a command creating a new
 * entity could be modified by a service to add extra metadata to the entity.
 */
abstract readonly class Command extends Value implements Message {
	use ReadonlyMessageKit;

	/**
	 * Create the Command and initialize the metadata.
	 */
	public function __construct() {
		$this->meta = new MessageMetadata();
	}
}
