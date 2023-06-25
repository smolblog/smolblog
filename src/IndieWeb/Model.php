<?php

namespace Smolblog\IndieWeb;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;

class Model extends DomainModel {
	public const SERVICES = [
		Micropub\MicropubService::class => [
			'env' => ApiEnvironment::class,
			'bus' => MessageBus::class,
		],
	];
}
