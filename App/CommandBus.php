<?php

namespace Smolblog\App;

use League\Tactician\CommandBus as LeagueCommander;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
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

		$runMethodInflector = new class implements MethodNameInflector {
			/**
			 * Provide the method name to call.
			 *
			 * @param mixed $command        Ignored.
			 * @param mixed $commandHandler Ignored.
			 * @return string always returns 'run'.
			 */
			public function inflect(mixed $command, mixed $commandHandler) {
				return 'run';
			}
		};

		$commandHandlerMiddleware = new CommandHandlerMiddleware(
			new ClassNameExtractor(),
			$this->locator,
			$runMethodInflector,
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
