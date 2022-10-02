<?php

namespace Smolblog\Core\Command;

use League\Tactician\CommandBus as LeagueCommander;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Plugins\LockingMiddleware;
use Smolblog\Core\Container\Container;

/**
 * Handles taking command classes and sending them to their handlers.
 */
class CommandBus {
	/**
	 * Tactician CommandBus
	 *
	 * @var LeagueCommander
	 */
	private LeagueCommander $internal;

	/**
	 * Construct the CommandBus
	 *
	 * @param array     $map       Array of Command => Handler.
	 * @param Container $container DI Container with handler classes.
	 */
	public function __construct(array $map, Container $container) {
		$commandHandlerMiddleware = new CommandHandlerMiddleware(
			new ClassNameExtractor(),
			new ContainerLocator($container, $map),
			new HandleClassNameInflector(),
		);

		$this->internal = new LeagueCommander([
			new LockingMiddleware(),
			$commandHandlerMiddleware,
		]);
	}

	/**
	 * Pass the given Command to its defined handler.
	 *
	 * @param Command $command Command to execute.
	 * @return mixed Result from the handler if any.
	 */
	public function handle(Command $command): mixed {
		return $this->internal->handle($command);
	}
}
