<?php

namespace Smolblog\Framework\Messages;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Indicates that an object provides a Query to check whether it can be executed.
 *
 * The Message class already includes stopMessage().
 */
interface AuthorizableMessage {
	/**
	 * Provide a Query object that will provide a truthy value if this object can be run.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query;

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
