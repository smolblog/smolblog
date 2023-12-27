<?php

namespace Smolblog\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Framework\Infrastructure\HttpSigner;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Domain model for ActivityPub
 */
class Model extends DomainModel {
	public const SERVICES = [
		Api\GetActor::class => [
			'bus' => MessageBus::class,
			'env' => ApiEnvironment::class,
			'log' => LoggerInterface::class,
		],
		Api\SharedInbox::class => [
			'service' => InboxService::class,
			'env' => ApiEnvironment::class,
			'log' => LoggerInterface::class,
		],
		Api\SiteInbox::class => [
			'service' => InboxService::class,
			'log' => LoggerInterface::class,
		],
		Api\Webfinger::class => [
			'bus' => MessageBus::class,
			'env' => ApiEnvironment::class,
		],

		Follow\FollowService::class => [
			'bus' => MessageBus::class,
			'fetcher' => ClientInterface::class,
			'signer' => HttpSigner::class,
			'env' => ApiEnvironment::class,
		],
		Follow\ActivityPubFollowerProvider::class => [
			'bus' => MessageBus::class,
			'fetcher' => ClientInterface::class,
			'env' => ApiEnvironment::class,
			'at' => ActivityTypesConverter::class,
			'signer' => HttpSigner::class,
		],

		ActivityTypesConverter::class => [
			'md' => SmolblogMarkdown::class,
			'env' => ApiEnvironment::class,
		],
		InboxService::class => [
			'bus' => MessageBus::class,
			'fetcher' => ClientInterface::class,
		],
	];
}
