<?php

namespace Smolblog\Framework\Messages;

use JsonSerializable;
use Psr\EventDispatcher\StoppableEventInterface;
use Smolblog\Framework\Objects\ArraySerializable;
use Smolblog\Framework\Objects\SerializableKit;

/**
 * Messages are structured objects for passing data between different layers or domains.
 *
 * Messages are essentially Value objects except for being mutable. Properties should be readonly whenever possible,
 * but messages themselves often have a need to be modified mid-flight, even if only to stop a message.
 *
 * @deprecated Migrate to Smolblog\Foundation classes
 */
abstract class Message implements StoppableEventInterface, ArraySerializable, JsonSerializable {
	use SerializableKit;

	/**
	 * True if message is stopped. Default false.
	 *
	 * @var boolean
	 */
	private bool $messageStopped = false;

	/**
	 * Tell the message to stop executing.
	 *
	 * Called if the result of getAuthorizationQuery resolves to false. Upon calling, the message object should set
	 * whatever internal property needed to correctly return `true` for isPropagationStopped().
	 *
	 * @return void
	 */
	public function stopMessage(): void {
		$this->messageStopped = true;
	}

	/**
	 * Is propagation stopped?
	 *
	 * From StoppableEventInterface. This will typically only be used by the Dispatcher to determine if the previous
	 * listener halted propagation.
	 *
	 * @return boolean
	 *   True if the Event is complete and no further listeners should be called.
	 *   False to continue calling listeners.
	 */
	public function isPropagationStopped(): bool {
		return $this->messageStopped;
	}

	/**
	 * Serialize the message.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$data = get_object_vars($this);
		unset($data['messageStopped']);

		return $data;
	}
}
