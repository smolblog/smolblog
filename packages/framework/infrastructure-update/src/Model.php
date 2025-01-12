<?php

namespace Smolblog\Infrastructure;

use Crell\Tukio\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Value\Http\HttpVerb;
use Smolblog\Infrastructure\OpenApi\OpenApiGenerator;

/**
 * Declared dependencies for the default infrastructure.
 *
 * You may override a few of these or omit this model entirely and add the services to your application's model.
 */
class Model extends DomainModel {
	public const AUTO_SERVICES = [
		Endpoint\EndpointRegistry::class,
		Registries\CommandHandlerRegistry::class,
		Registries\EventListenerRegistry::class,
		OpenApi\OpenApiGenerator::class,
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

	public static function scratchpad() {
		$gen = new OpenApiGenerator();
		echo \json_encode($gen->componentSchemaFromClass(HttpVerb::class), JSON_PRETTY_PRINT);
	}
}
