<?php

namespace Smolblog\Core;

use Psr\EventDispatcher\EventDispatcherInterface;

interface EventDispatcher extends EventDispatcherInterface {
	/**
	 * Add a listener to an event
	 *
	 * @param string   $eventIdentifier ID for event (usually fully qualified class name).
	 * @param callable $listener        Code to call upon dispatch.
	 * @param integer  $priority        Priority for this listener. Higher priorities go first; default 0.
	 * @return void
	 */
	public function subscribeTo(string $eventIdentifier, callable $listener, int $priority = 0);
}
