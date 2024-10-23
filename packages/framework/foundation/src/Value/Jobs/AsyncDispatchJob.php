<?php

namespace Smolblog\Foundation\Value\Jobs;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * A job that dispatches a DomainEvent.
 */
readonly class AsyncDispatchJob extends Job {
	/**
	 * Create a job that dispatches an event.
	 *
	 * @param DomainEvent $event Event to dispatch.
	 */
	public function __construct(public DomainEvent $event) {
		parent::__construct(
			service: EventDispatcherInterface::class,
			method: 'dispatch',
		);
	}
}
