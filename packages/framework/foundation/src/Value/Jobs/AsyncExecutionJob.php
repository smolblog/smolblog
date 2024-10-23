<?php

namespace Smolblog\Foundation\Value\Jobs;

use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * A job that executes a Command.
 */
readonly class AsyncExecutionJob extends Job {
	/**
	 * Create a job that executes a Command.
	 *
	 * @param Command $command Event to dispatch.
	 */
	public function __construct(public Command $command) {
		parent::__construct(
			service: CommandBus::class,
			method: 'execute',
		);
	}
}
