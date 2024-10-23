<?php

namespace Smolblog\Foundation\Service\Command;

use Smolblog\Foundation\Value\Messages\Command;

/**
 * A service that accepts a Command object and routes it to the correct handler.
 *
 * There are two methods required, one for synchronous execution and one for asychronous execution. How async
 * execution is achieved is left to the implementation, but the intent is to allow the current process to complete
 * without waiting for the async command to complete.
 */
interface CommandBus {
	/**
	 * Execute the given command.
	 *
	 * This method should first route the Command through any appropriate middleware before finally calling the
	 * correct handler for the Command.
	 *
	 * @param Command $command Command to execute.
	 * @return mixed Optional result of the execution.
	 */
	public function execute(Command $command): mixed;
}
