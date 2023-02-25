<?php

namespace Smolblog\Framework\Infrastructure;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Default model with services provided by the Framework.
 */
class DefaultModel extends DomainModel {
	public const SERVICES = [
		MessageBus::class => DefaultMessageBus::class,
		DefaultMessageBus::class => ['provider' => ListenerProviderInterface::class],
		ListenerProviderInterface::class => ListenerRegistry::class,
		ListenerRegistry::class => ['container' => ContainerInterface::class],
		QueryMemoizationService::class => [],
		SecurityCheckService::class => ['messageBus' => MessageBus::class],
		SmolblogMarkdown::class => [],
	];
}
