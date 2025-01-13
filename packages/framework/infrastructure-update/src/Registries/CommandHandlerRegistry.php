<?php

namespace Smolblog\Infrastructure\Registries;

use Exception;
use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Register Command Handler services and map the Commands they are responsible for.
 */
class CommandHandlerRegistry implements Registry, CommandBus {
	/**
	 * Store the Command classes and their handler methods.
	 *
	 * @var array<string, string[]>
	 */
	private array $library = [];

	/**
	 * Get the interface this Registry tracks.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return CommandHandlerService::class;
	}

	/**
	 * Create the registry.
	 *
	 * @param ContainerInterface $container DI Container with the services to be registered.
	 */
	public function __construct(private ContainerInterface $container) {
	}

	/**
	 * Accept the configuration for the registry.
	 *
	 * @param class-string<CommandHandlerService>[] $configuration Array of classes to register.
	 * @return void
	 */
	public function configure(array $configuration): void {
		array_walk($configuration, fn($srv) => $this->autoregisterService($srv));
	}

	/**
	 * Execute the given command.
	 *
	 * This method should first route the Command through any appropriate middleware before finally calling the
	 * correct handler for the Command.
	 *
	 * @throws Exception When no handler exists for the command.
	 *
	 * @param Command $command Command to execute.
	 * @return mixed Optional result of the execution.
	 */
	public function execute(Command $command): mixed {
		$commandName = \get_class($command);

		if (!\array_key_exists($commandName, $this->library)) {
			// How do we want to do this?
			throw new Exception("No handler exists for $commandName.");
		}

		$entry = $this->library[$commandName];
		return \call_user_func(
			[$this->container->get($entry['class']), $entry['method']],
			$command
		);
	}

	/**
	 * Reflect the given service and register it for the commands it handles.
	 *
	 * @param string $className Service to reflect and register.
	 * @return void
	 */
	private function autoregisterService(string $className) {
		$refClass = new ReflectionClass($className);
		foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $refMethod) {
			$this->registerMethod($className, $refMethod);
		}
	}

	/**
	 * Register the given method for the given class if it is a CommandHandler.
	 *
	 * Checks for the CommandHandler attribute and skips if not present. A CommandHandler method must have exactly one
	 * required parameter typehinted to the Command(s) it handles. Exceptions will be thrown if the method has multiple
	 * required parameters, or the type is an intersection type or not present. Union types will register the method for
	 * each type.
	 *
	 * @throws CodePathNotSupported If the CommandHandler attribute is present but the requirements are not met.
	 *
	 * @param class-string     $className Name of the class the method belongs to.
	 * @param ReflectionMethod $refMethod Reflection of the method being registered.
	 * @return void
	 */
	private function registerMethod(string $className, ReflectionMethod $refMethod) {
		$refAtts = $refMethod->getAttributes(CommandHandler::class, ReflectionAttribute::IS_INSTANCEOF);
		if (empty($refAtts)) {
			return;
		}

		if ($refMethod->getNumberOfRequiredParameters() !== 1) {
			throw new CodePathNotSupported(
				message: 'A command handler can only require the command object.',
				location: "$className::{$refMethod->getName()}",
			);
		}

		$param = $refMethod->getParameters()[0];
		$paramType = $param->getType();
		if (!$paramType instanceof ReflectionNamedType && !$paramType instanceof ReflectionUnionType) {
			throw new CodePathNotSupported(
				message: 'A command handler must accept one or more command object types.',
				location: "$className::{$refMethod->getName()}",
			);
		}

		$commands = $paramType instanceof ReflectionUnionType ?
			\array_map(fn($refType) => $refType->getName(), $paramType->getTypes()) :
			[$paramType->getName()];

		foreach ($commands as $command) {
			$this->setHandler(
				command: $command,
				class: $className,
				method: $refMethod->getName(),
			);
		}
	}

	/**
	 * Save the handler for a command.
	 *
	 * @param class-string $command Command being handled.
	 * @param class-string $class   Class the method belongs to.
	 * @param string       $method  CommandHandler method.
	 * @return void
	 */
	private function setHandler(string $command, string $class, string $method) {
		$this->library[$command] = ['class' => $class, 'method' => $method];
	}
}
