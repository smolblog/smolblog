<?php

namespace Smolblog\IndieWeb;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;

/**
 * IndieWeb domain model definition.
 */
class Model extends DomainModel {
	public const SERVICES = [
		Api\Micropub::class => [
			'micropub' => Micropub\MicropubService::class
		],
		Api\MicropubMedia::class => [
			'micropub' => Micropub\MicropubService::class
		],
		Micropub\MicropubService::class => [
			'env' => ApiEnvironment::class,
			'bus' => MessageBus::class,
			'mf' => MicroformatsConverter::class,
		],
		MicroformatsConverter::class => [],
	];
}
