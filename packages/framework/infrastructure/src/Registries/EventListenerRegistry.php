<?php

namespace Smolblog\Infrastructure\Registries;

use Crell\Tukio\OrderedListenerProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Smolblog\Foundation\Service\Event\EventListenerService;
use Smolblog\Foundation\Service\Registry\Registry;

/**
 * Registrar for message listeners.
 *
 * Message listeners are callables that take a message object as its sole parameter. The Registrar will take object
 * inheritance and interfaces into account when deciding which listeners will respond to which object. For example,
 * a listener that will respond to an Event object will respond to all messages that inherit from Event.
 */
class EventListenerRegistry implements ListenerProviderInterface, Registry {
	/**
	 * This registry registers Listener classes.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return EventListenerService::class;
	}

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
	 * Accept the configuration for the registry.
	 *
	 * @param string[] $configuration Array of classes to register.
	 * @return void
	 */
	public function configure(array $configuration): void {
		array_walk($configuration, fn($srv) => $this->internal->addSubscriber($srv, $srv));
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
