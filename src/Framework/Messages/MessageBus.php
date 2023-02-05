<?php

namespace Smolblog\Framework\Messages;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Framework\Messages\Query;

/**
 * Handles the sending of messages to the appropriate objects.
 *
 * A simple wrapper around a PSR-14 Event Dispatcher. Adds one convenience method for queries to automatically
 * unpack and return the results.
 */
interface MessageBus extends EventDispatcherInterface {
	/**
	 * Dispatch the given message to its listeners.
	 *
	 * @param mixed $message Message to send.
	 * @return mixed Message potentially modified by listeners.
	 */
	public function dispatch(mixed $message): mixed;

	/**
	 * Convenience method for sending Query messages that will return the results.
	 *
	 * @param Query $query Query to execute.
	 * @return mixed Results of the query.
	 */
	public function fetch(Query $query): mixed;
}
