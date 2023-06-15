<?php

namespace Smolblog\ActivityPub;

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
		Api\Webfinger::class => [
			'bus' => MessageBus::class,
			'env' => ApiEnvironment::class,
		],
	];
}
