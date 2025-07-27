<?php

namespace Smolblog\Test\BasicApp;

use Crell\Tukio\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Service\Job\JobManager;
use Smolblog\Infrastructure\Registries\CommandHandlerRegistry;
use Smolblog\Infrastructure\Registries\EventListenerRegistry;
use Smolblog\Test\BasicApp\TestJobManager;

/**
 * Basic infrastructure used by tests.
 */
class Model extends DomainModel {
	public const AUTO_SERVICES = [
		CommandHandlerRegistry::class,
		EventListenerRegistry::class,
		TestJobManager::class,
	];

	public const SERVICES = [
		ListenerProviderInterface::class => EventListenerRegistry::class,
		EventDispatcherInterface::class => Dispatcher::class,
		CommandBus::class => CommandHandlerRegistry::class,
		Dispatcher::class => [ListenerProviderInterface::class],
		JobManager::class => TestJobManager::class,
	];
}
