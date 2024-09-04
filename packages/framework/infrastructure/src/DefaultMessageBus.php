<?php

namespace Smolblog\Framework\Infrastructure;

use Crell\Tukio\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Service\Query\QueryBus;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Framework\Messages\Command as DeprecatedCommand;
use Smolblog\Framework\Messages\Message as DeprecatedMessage;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Messages\Query as DeprecatedQuery;

/**
 * Handles the sending of messages to the appropriate objects.
 *
 * A simple wrapper around a PSR-14 Event Dispatcher. Adds one convenience method for queries to automatically
 * unpack and return the results. Takes a PSR-14-compliant Listener Provider in construction.
 */
class DefaultMessageBus implements MessageBus, CommandBus, QueryBus, EventDispatcherInterface {
	/**
	 * Internal PSR-14-compliant dispatcher.
	 *
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $internal;

	/**
	 * Create the MessageBus with a given listener provider.
	 *
	 * @param ListenerProviderInterface $provider PSR-14-compliant provider.
	 * @param LoggerInterface           $log      PSR-3 logger.
	 */
	public function __construct(
		ListenerProviderInterface $provider,
		private LoggerInterface $log = new NullLogger(),
	) {
		$this->internal = new Dispatcher($provider, $log);
	}

	/**
	 * Dispatch the given message to its listeners.
	 *
	 * @param object $message Message to send.
	 * @return mixed Message potentially modified by listeners.
	 */
	public function dispatch(object $message): mixed {
		return $this->internal->dispatch($message);
	}

	/**
	 * Convenience method for sending Query messages that will return the results.
	 *
	 * @param DeprecatedQuery|Query $query Query to execute.
	 * @return mixed Results of the query.
	 */
	public function fetch(DeprecatedQuery|Query $query): mixed {
		return $this->internal->dispatch($query)->results();
	}

	/**
	 * Dispatch the given message on a separate thread.
	 *
	 * No guidance is given where said thread is. It could be a queued job on the same thread, it could be a managed
	 * queue, it could be an entirely different server. As such, the given Message should have as much information
	 * included as reasonably possible.
	 *
	 * @param DeprecatedMessage|Message $message Message to send.
	 * @return void
	 */
	public function dispatchAsync(DeprecatedMessage|Message $message): void {
		$this->internal->dispatch(new AsyncWrappedMessage($message));
	}

	/**
	 * Execute the given command,
	 *
	 * @param Command $command Command to execute.
	 * @return mixed
	 */
	public function execute(Command $command): mixed {
		$retVal = $this->dispatch($command);
		return $retVal->returnValue() ?? $retVal;
	}

	/**
	 * Execute the given command on a separate thread.
	 *
	 * @param Command|DeprecatedCommand $command Command to execute.
	 * @return void
	 */
	public function executeAsync(Command|DeprecatedCommand $command): void {
		$this->dispatchAsync($command);
	}
}
