<?php

namespace Smolblog\Framework;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Object that can pass an event to its handling services.
 *
 * Like CommandBus and QueryBus, this takes an informational object and sends it to an appropriate service. Unlike
 * Commands and Queries, hooks do NOT have to be read-only and in fact are intended to be editable in many cases.
 */
interface HookBus extends EventDispatcherInterface {
	/**
	 * Provide all relevant listeners with an event to process.
	 *
	 * @param object $event The object to process.
	 * @return object The Event that was passed, now modified by listeners.
	 */
	public function dispatch(object $event);
}
