<?php

namespace Smolblog\Framework\Messages;

/**
 * Trait to help events easily implement AuthorizableMessage and/or StoppableEventInterface
 */
trait StoppableMessageKit {
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
}
