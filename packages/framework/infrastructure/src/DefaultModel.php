<?php

namespace Smolblog\Framework\Infrastructure;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Framework\ActivityPub\MessageSender;
use Smolblog\Framework\ActivityPub\Signatures\MessageSigner;
use Smolblog\Framework\ActivityPub\Signatures\MessageVerifier;
use Smolblog\Framework\ActivityPub\ObjectGetter;
use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Markdown\SmolblogMarkdown;
use Smolblog\Foundation\Service\Query\QueryBus;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Default model with services provided by the Framework.
 */
class DefaultModel extends DomainModel {
	public const SERVICES = [
		MessageBus::class => DefaultMessageBus::class,
		EventDispatcherInterface::class => DefaultMessageBus::class,
		CommandBus::class => DefaultMessageBus::class,
		QueryBus::class => DefaultMessageBus::class,
		DefaultMessageBus::class => ['provider' => ListenerProviderInterface::class, 'log' => LoggerInterface::class],
		ListenerProviderInterface::class => ListenerRegistry::class,
		ListenerRegistry::class => ['container' => ContainerInterface::class],
		QueryMemoizationService::class => [],
		SecurityCheckService::class => ['messageBus' => MessageBus::class],
		SmolblogMarkdown::class => [],
		KeypairGenerator::class => [],
		LoggerInterface::class => NullLogger::class,
		NullLogger::class => [],
		MessageSender::class => [
			'fetcher' => ClientInterface::class,
			'signer' => MessageSigner::class,
			'log' => LoggerInterface::class,
		],
		MessageSigner::class => [],
		MessageVerifier::class => [],
		ObjectGetter::class => [
			'fetcher' => ClientInterface::class,
			'signer' => MessageSigner::class,
		],
	];
}
