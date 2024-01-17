<?php

namespace Smolblog\ActivityPub;

use Psr\Log\LoggerInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Framework\ActivityPub\MessageSender;
use Smolblog\Framework\ActivityPub\ObjectGetter;
use Smolblog\Framework\ActivityPub\Signatures\MessageSigner;
use Smolblog\Framework\ActivityPub\Signatures\MessageVerifier;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Domain model for ActivityPub
 */
class Model extends DomainModel {
	public const SERVICES = [
		Api\ActorFollowers::class => [
			'bus' => MessageBus::class,
			'env' => ApiEnvironment::class,
		],
		Api\GetActor::class => [
			'bus' => MessageBus::class,
			'env' => ApiEnvironment::class,
			'log' => LoggerInterface::class,
		],
		Api\SharedInbox::class => [
			'service' => InboxService::class,
		],
		Api\SiteFollowers::class => [
			'bus' => MessageBus::class,
			'env' => ApiEnvironment::class,
		],
		Api\SiteInbox::class => [
			'service' => InboxService::class,
		],
		Api\Webfinger::class => [
			'bus' => MessageBus::class,
			'env' => ApiEnvironment::class,
		],

		Follow\FollowService::class => [
			'bus' => MessageBus::class,
			'env' => ApiEnvironment::class,
			'sender' => MessageSender::class,
		],
		Follow\ActivityPubFollowerProvider::class => [
			'bus' => MessageBus::class,
			'env' => ApiEnvironment::class,
			'at' => ActivityTypesConverter::class,
			'sender' => MessageSender::class,
		],

		ActivityTypesConverter::class => [
			'md' => SmolblogMarkdown::class,
			'env' => ApiEnvironment::class,
		],
		InboxService::class => [
			'bus' => MessageBus::class,
			'at' => ActivityTypesConverter::class,
			'getter' => ObjectGetter::class,
			'signer' => MessageVerifier::class,
			'log' => LoggerInterface::class,
		],
	];
}
