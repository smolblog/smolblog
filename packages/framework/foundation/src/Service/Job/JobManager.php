<?php

namespace Smolblog\Foundation\Service\Job;

use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Jobs\Job;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Service to add the given Job to a queue and
 */
interface JobManager extends Service {
	/**
	 * Add the given Job to a queue to execute on a separate thread.
	 *
	 * @param Job $job Job to enqueue.
	 * @return void
	 */
	public function enqueue(Job $job): void;

	/**
	 * Dispatch the given Event on a separate thread.
	 *
	 * @param DomainEvent $event Event to dispatch.
	 * @return void
	 */
	public function dispatchAsync(DomainEvent $event): void;

	/**
	 * Execute the given command on a separate thread.
	 *
	 * @param Command $command Command to execute.
	 * @return void
	 */
	public function executeAsync(Command $command): void;
}
