<?php

namespace Smolblog\IndieWeb;

use Psr\Log\LoggerInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\ContentV1\ContentTypeRegistry;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Framework\Objects\DomainModel;

/**
 * IndieWeb domain model definition.
 */
class Model extends DomainModel {
	public const SERVICES = [
		Api\Micropub::class => [
			'micropub' => Micropub\MicropubService::class,
			'log' => LoggerInterface::class,
		],
		Api\MicropubMedia::class => [
			'micropub' => Micropub\MicropubService::class,
		],
		Micropub\MicropubService::class => [
			'env' => ApiEnvironment::class,
			'bus' => MessageBus::class,
			'mf' => MicroformatsConverter::class,
			'typeReg' => ContentTypeRegistry::class,
			'log' => LoggerInterface::class,
		],
		MicroformatsConverter::class => [],
	];
}
