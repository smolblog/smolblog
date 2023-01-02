<?php

namespace Smolblog\Framework\Infrastructure;

use Crell\Tukio\OrderedListenerProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Registrar for message listeners.
 *
 * Message listeners are callables that take a message object as its sole parameter. The Registrar will take object
 * inheritance and interfaces into account when deciding which listeners will respond to which object. For example,
 * a listener that will respond to an Event object will respond to all messages that inherit from Event.
 *
 * Priority can be set using the attributes in the Smolblog\Framework\Infrastructure\Attributes namespace. The 5 layers
 * in order of execution are
 *
 * 1. Security
 * 2. Check Memo
 * 3. Event Store
 * 4. Execution (default)
 * 5. Save Memo
 *
 * To add a listener to a particular layer, add the appropriate attribute (ex: `#[SecurityLayerListener]`). Within
 * those layers, a listener can be moved earlier or later in the priority queue by setting the `earlier:` or
 * `later:` parameters (ex: `#[EventStoreLayerListener(earlier: 3)]` would be 3 places higher in the priority queue
 * than a default listener in that layer).
 */
class ListenerRegistrar implements ListenerProviderInterface {
	/**
	 * Internal OrderedListenerProvider
	 *
	 * @var OrderedListenerProvider
	 */
	private OrderedListenerProvider $internal;

	/**
	 * Create the Registry
	 *
	 * @param ContainerInterface $container Dependency Injection container with the required services.
	 */
	public function __construct(ContainerInterface $container) {
		$this->internal = new OrderedListenerProvider(container: $container);
	}

	/**
	 * Register the given callable as a listener.
	 *
	 * A callable that takes a message object as its single parameter. The registry will note the parameter type and
	 * wire things up accordingly.
	 *
	 * @param callable $listener Class registered in the container.
	 * @return void
	 */
	public function registerCallable(callable $listener): void {
		$this->internal->addListener($listener);
	}

	/**
	 * Register the given class as a listener service.
	 *
	 * A listener service contains methods either
	 *   1. beginning with `on` or
	 *   2. containing a timing attribute from the Smolblog\Framework\Messages\Attributes namespace
	 * that take a message object as its single parameter. The registry will note the parameter type and wire things up
	 * accordingly.
	 *
	 * @param string $className Class registered in the container.
	 * @return void
	 */
	public function registerService(string $className): void {
		$this->internal->addSubscriber($className, $className);
	}

	/**
	 * Used by the event dispatcher to get the appropriate listeners for the given event.
	 *
	 * @param object $event Event being dispatched.
	 * @return iterable
	 */
	public function getListenersForEvent(object $event): iterable {
		return $this->internal->getListenersForEvent($event);
	}
}
