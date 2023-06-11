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
	 * @param object $message Message to send.
	 * @return mixed Message potentially modified by listeners.
	 */
	public function dispatch(object $message): mixed;

	/**
	 * Convenience method for sending Query messages that will return the results.
	 *
	 * @param Query $query Query to execute.
	 * @return mixed Results of the query.
	 */
	public function fetch(Query $query): mixed;

	/**
	 * Dispatch the given message on a separate thread.
	 *
	 * No guidance is given where said thread is. It could be a queued job on the same thread, it could be a managed
	 * queue, it could be an entirely different server. As such, the given Message should have as much information
	 * included as reasonably possible.
	 *
	 * @param Message $message Message to send.
	 * @return void
	 */
	public function dispatchAsync(Message $message): void;

	// TODO: regenerate(Event): method that takes an Event and only calls Projection services.
}
