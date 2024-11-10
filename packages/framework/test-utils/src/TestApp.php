<?php

namespace Smolblog\Test;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Service\Job\JobManager;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Framework\Infrastructure\AppKit;
use Smolblog\Framework\Infrastructure\DefaultMessageBus;
use Smolblog\Framework\Infrastructure\DefaultModel;
use Smolblog\Framework\Infrastructure\ServiceRegistry;

class TestApp {
	use AppKit;

	public readonly ServiceRegistry $container;

	public function __construct(array $models, array $services) {
		$this->container = new ServiceRegistry(
			$this->buildDependencyMapFromArrays([
				DefaultModel::getDependencyMap(),
				[
					JobManager::class => TestJobManager::class,
					TestJobManager::class => ['container' => ContainerInterface::class],
				],
				...array_map(fn($model) => $model::getDependencyMap(), $models),
				$services,
			])
		);
	}

	public function execute(Command $command): mixed {
		// Serialize and deserialize the Command to ensure that it will successfully translate.
		// Future systems may send Commands to other services.
		$serializedCommand = $command->serializeValue();
		$retval = $this->container->get(CommandBus::class)->execute(Command::deserializeValue($serializedCommand));
		$this->container->get(TestJobManager::class)->run();
		return $retval;
	}

	public function dispatch(mixed $event): mixed {
		$processedEvent = $event;
		if (is_a($event, DomainEvent::class)) {
			// Serialize and deserialize the DomainEvent to ensure that it will successfully translate.
			// Future systems may send DomainEvents to other services.
			$processedEvent = DomainEvent::deserializeValue($event->serializeValue());
		}

		$retval = $this->container->get(EventDispatcherInterface::class)->dispatch($processedEvent);
		$this->container->get(TestJobManager::class)->run();
		return $retval;
	}
}
