<?php

namespace Smolblog\Infrastructure;

use Crell\Tukio\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use ReflectionProperty;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Value\Http\HttpVerb;
use Smolblog\Infrastructure\Endpoint\EndpointConfiguration;
use Smolblog\Infrastructure\OpenApi\OpenApiGenerator;
use Smolblog\Infrastructure\OpenApi\OpenApiSpecInfo;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Declared dependencies for the default infrastructure.
 *
 * You may override a few of these or omit this model entirely and add the services to your application's model.
 */
class Model extends DomainModel {
	public const AUTO_SERVICES = [
		Endpoint\EndpointRegistry::class,
		MessageBus\CommandHandlerRegistry::class,
		MessageBus\EventListenerRegistry::class,
		OpenApi\OpenApiGenerator::class,
	];

	public const SERVICES = [
		LoggerInterface::class => NullLogger::class,
		ListenerProviderInterface::class => MessageBus\EventListenerRegistry::class,
		EventDispatcherInterface::class => Dispatcher::class,
		CommandBus::class => MessageBus\CommandHandlerRegistry::class,

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
