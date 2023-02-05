<?php

namespace Smolblog\Framework\Messages;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Indicates that a message can be stopped, preventing further listeners from receiving it.
 *
 * An extension to StoppableEventInterface that provides a way to stop the message/event. The StoppableMessageKit
 * trait provides the stopMessage() and isPropagationStopped() functions.
 */
interface StoppableMessage extends StoppableEventInterface {
	/**
	 * Tell the message to stop executing.
	 *
	 * Called if the result of getAuthorizationQuery resolves to false. Upon calling, the message object should set
	 * whatever internal property needed to correctly return `false` for isPropagationStopped().
	 *
	 * @return void
	 */
	public function stopMessage(): void;
}
