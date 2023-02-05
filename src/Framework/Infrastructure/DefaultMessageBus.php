<?php

namespace Smolblog\Framework\Infrastructure;

use Crell\Tukio\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Messages\Query;

/**
 * Handles the sending of messages to the appropriate objects.
 *
 * A simple wrapper around a PSR-14 Event Dispatcher. Adds one convenience method for queries to automatically
 * unpack and return the results. Takes a PSR-14-compliant Listener Provider in construction.
 */
class DefaultMessageBus implements MessageBus {
	/**
	 * Internal PSR-14-compliant dispatcher.
	 *
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $internal;

	/**
	 * Create the MessageBus with a given listener provider.
	 *
	 * @param ListenerProviderInterface $provider PSR-14-compliant provider.
	 */
	public function __construct(ListenerProviderInterface $provider = null) {
		$this->internal = new Dispatcher($provider);
	}

	/**
	 * Dispatch the given message to its listeners.
	 *
	 * @param mixed $message Message to send.
	 * @return mixed Message potentially modified by listeners.
	 */
	public function dispatch(mixed $message): mixed {
		return $this->internal->dispatch($message);
	}

	/**
	 * Convenience method for sending Query messages that will return the results.
	 *
	 * @param Query $query Query to execute.
	 * @return mixed Results of the query.
	 */
	public function fetch(Query $query): mixed {
		return $this->internal->dispatch($query)->results;
	}
}
