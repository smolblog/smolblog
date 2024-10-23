<?php

namespace Smolblog\Foundation\Service\Job;

use Smolblog\Foundation\Value\Jobs\AsyncDispatchJob;
use Smolblog\Foundation\Value\Jobs\AsyncExecutionJob;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Add basic implementations of the -Async methods in JobManager.
 */
trait JobManagerKit {
	/**
	 * Dispatch the given Event on a separate thread.
	 *
	 * Must be a DomainEvent to guarantee serialization.
	 *
	 * @param DomainEvent $event Event to dispatch.
	 * @return void
	 */
	public function dispatchAsync(DomainEvent $event): void {
		$this->enqueue(new AsyncDispatchJob($event));
	}

	/**
	 * Execute the given command on a separate thread.
	 *
	 * @param Command $command Command to execute.
	 * @return void
	 */
	public function executeAsync(Command $command): void {
		$this->enqueue(new AsyncExecutionJob($command));
	}
}
