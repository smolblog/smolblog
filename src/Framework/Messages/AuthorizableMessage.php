<?php

namespace Smolblog\Framework\Messages;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Indicates that an object provides a Query to check whether it can be executed.
 *
 * Also includes the StoppableMessage interface for standard halting of a message that is not authorized. The
 * StoppableMessageKit trait provides the interruptExecution() and isPropagationStopped() functions.
 */
interface AuthorizableMessage extends StoppableMessage {
	/**
	 * Provide a Query object that will provide a truthy value if this object can be run.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query;
}
