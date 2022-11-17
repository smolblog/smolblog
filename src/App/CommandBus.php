<?php

namespace Smolblog\App;

use League\Tactician\CommandBus as LeagueCommander;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Plugins\LockingMiddleware;
use Smolblog\App\Container\Container;
use Smolblog\Framework\Command;

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
	 * Internal Locator class for maintaining command map.
	 *
	 * @var ContainerLocator
	 */
	private ContainerLocator $locator;

	/**
	 * Construct the CommandBus
	 *
	 * @param Container $container DI Container with handler classes.
	 * @param array     $map       Array of Command => Handler.
	 */
	public function __construct(Container $container, array $map = []) {
		$this->locator = new ContainerLocator($container, $map);

		$commandHandlerMiddleware = new CommandHandlerMiddleware(
			new ClassNameExtractor(),
			$this->locator,
			new HandleClassNameInflector(),
		);

		$this->internal = new LeagueCommander([
			new LockingMiddleware(),
			$commandHandlerMiddleware,
		]);
	}

	/**
	 * Map a command to a handler.
	 *
	 * @param string $command Fully-qualified class name of the command.
	 * @param string $handler Fully-qualified class name of the handler.
	 * @return void
	 */
	public function map(string $command, string $handler): void {
		$this->locator->addHandler($handler, $command);
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
