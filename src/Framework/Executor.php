<?php

namespace Smolblog\Framework;

/**
 * Object that can pass a Command to its handling service.
 *
 * A Command object passed to either of the `exec` methods should be routed to the appropriate service and passed to
 * its `run` method.
 *
 * This is infrastructure instead of in App because sometimes the Domain Model needs to tell itself to do something.
 */
interface Executor {
	/**
	 * Execute the given Command by passing it to its service.
	 *
	 * @param Command $command Command to execute.
	 * @return mixed Result of the Command, if any.
	 */
	public function exec(Command $command): mixed;

	/**
	 * Execute the given Command in a separate process.
	 *
	 * Good for long-running or background processes that should not block the main process.
	 *
	 * @param Command $command Command to execute.
	 * @return void
	 */
	public function execAsync(Command $command): void;
}
