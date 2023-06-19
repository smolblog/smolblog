<?php

namespace Smolblog\ActivityPub;

use Psr\Http\Client\ClientInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;

/**
 * Domain model for ActivityPub
 */
class Model extends DomainModel {
	public const SERVICES = [
		Api\GetActor::class => [
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

		InboxService::class => [
			'bus' => MessageBus::class,
			'fetcher' => ClientInterface::class,
		],
	];
}
