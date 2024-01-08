<?php

namespace Smolblog\Framework\Infrastructure;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smolblog\Framework\ActivityPub\MessageSender;
use Smolblog\Framework\ActivityPub\MessageSigner;
use Smolblog\Framework\ActivityPub\MessageVerifier;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Default model with services provided by the Framework.
 */
class DefaultModel extends DomainModel {
	/**
	 * Basic function to return false.
	 *
	 * @return boolean
	 */
	public function false(): bool {
		return false;
	}

	public const SERVICES = [
		MessageBus::class => DefaultMessageBus::class,
		DefaultMessageBus::class => ['provider' => ListenerProviderInterface::class, 'log' => LoggerInterface::class],
		ListenerProviderInterface::class => ListenerRegistry::class,
		ListenerRegistry::class => ['container' => ContainerInterface::class],
		QueryMemoizationService::class => [],
		SecurityCheckService::class => ['messageBus' => MessageBus::class],
		SmolblogMarkdown::class => [],
		HttpSigner::class => [],
		KeypairGenerator::class => [],
		LoggerInterface::class => NullLogger::class,
		NullLogger::class => [],
		MessageSender::class => [
			'fetcher' => ClientInterface::class,
			'signer' => MessageSigner::class,
			'log' => LoggerInterface::class,
			// 'throwOnError' => self::class . '::false',
		],
		MessageSigner::class => HttpSigner::class,
		MessageVerifier::class => HttpSigner::class,
	];
}
