<?php

namespace Smolblog\Foundation\Value\Messages;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Foundation\Value\Traits\ReadonlyMessageKit;
use Smolblog\Foundation\Value\Traits\SerializableSupertypeKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;

/**
 * A command is a message that is sent to a service to perform an action.
 *
 * Commands are typically sent to a service to perform an action, such as creating a new user, or updating a user's
 * profile.
 */
abstract readonly class Command extends Value implements SerializableValue, Message {
	use ReadonlyMessageKit;
	use SerializableSupertypeKit;

	/**
	 * Create the Command and initialize the metadata.
	 */
	public function __construct() {
		$this->meta = new MessageMetadata();
	}

	/**
	 * Set the return value of the command.
	 *
	 * @deprecated Stay tuned for actual return values
	 *
	 * @param mixed $value Value to return.
	 * @return void
	 */
	public function setReturnValue(mixed $value): void {
		$this->meta->setMetaValue('return', $value);
	}

	/**
	 * Get the return value of the command.
	 *
	 * @deprecated Stay tuned for actual return values
	 *
	 * @return mixed
	 */
	public function returnValue(): mixed {
		return $this->meta->getMetaValue('return');
	}
}
