<?php

namespace Smolblog\Foundation\Value\Messages;

use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageKit;

/**
 * A command is a message that is sent to a service to perform an action.
 *
 * Commands are typically sent to a service to perform an action, such as creating a new user, or updating a user's
 * profile. Commands are also used to send events to the system, such as when a user creates a new post.
 *
 * Not `readonly` to allow middleware/plugins to modify parts of the Command. For example, a command creating a new
 * entity could be modified by a service to add extra metadata to the entity.
 */
abstract class Command implements Message {
	use MessageKit;
}
