<?php

namespace Smolblog\Infrastructure;

use Crell\Tukio\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\Command\CommandBus;

/**
 * Declared dependencies for the default infrastructure.
 *
 * You may override a few of these or omit this model entirely and add the services to your application's model.
 */
class Model extends DomainModel {
	public const AUTO_SERVICES = [
		Registries\CommandHandlerRegistry::class,
		Registries\EventListenerRegistry::class,
	];

	public const SERVICES = [
		LoggerInterface::class => NullLogger::class,
		ListenerProviderInterface::class => Registries\EventListenerRegistry::class,
		EventDispatcherInterface::class => Dispatcher::class,
		CommandBus::class => Registries\CommandHandlerRegistry::class,

		NullLogger::class => [],
		Dispatcher::class => [
			ListenerProviderInterface::class,
			LoggerInterface::class,
		]
	];
}
