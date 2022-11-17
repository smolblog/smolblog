<?php

namespace Smolblog\Core\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use League\Event\EventDispatcher as LeagueEventDispatcher;
use League\Event\ListenerPriority;

/**
 * Dispatcher to coordinate sending events to their subscribers.
 */
class EventDispatcher implements EventDispatcherInterface {
	/**
	 * Internal League EventDispatcher instance
	 *
	 * @var LeagueEventDispatcher
	 */
	private LeagueEventDispatcher $internal;

	public const PRIORITY_HIGH = ListenerPriority::HIGH;
	public const PRIORITY_NORMAL = ListenerPriority::NORMAL;
	public const PRIORITY_LOW = ListenerPriority::LOW;

	/**
	 * Create a new EventDispatcher
	 */
	public function __construct() {
		$this->internal = new LeagueEventDispatcher();
	}

	/**
	 * Provide all relevant listeners with an event to process.
	 *
	 * @param object $event The object to process.
	 *
	 * @return object The Event that was passed, now modified by listeners.
	 */
	public function dispatch(object $event): object {
		return $this->internal->dispatch($event);
	}

	/**
	 * Add a listener to an event
	 *
	 * @param string   $eventIdentifier ID for event (usually fully qualified class name).
	 * @param callable $listener        Code to call upon dispatch.
	 * @param integer  $priority        Priority for this listener. Higher priorities go first; default 0.
	 * @return void
	 */
	public function subscribeTo(string $eventIdentifier, callable $listener, int $priority = self::PRIORITY_NORMAL) {
		$this->internal->subscribeTo($eventIdentifier, $listener, $priority);
	}
}
