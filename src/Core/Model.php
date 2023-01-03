<?php

namespace Smolblog\Core;

use Psr\Container\ContainerInterface;
use Smolblog\Framework\MessageBus\MessageBus;
use Smolblog\Framework\Objects\DomainModel;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model extends DomainModel {
	public const SERVICES = [
		Connector\Services\AuthRequestService::class => [
			'connectors' => Connector\Services\ConnectorRegistrar::class,
			'stateRepo' => Connector\Services\AuthRequestStateRepo::class,
			'messageBus' => MessageBus::class,
		],
		Connector\Services\ChannelRefresher::class => [
			'messageBus' => MessageBus::class,
			'connectors' => Connector\Services\ConnectorRegistrar::class,
		],
		Connector\Services\ConnectionRefresher::class => [
			'messageBus' => MessageBus::class,
			'connectorRepo' => Connector\Services\ConnectorRegistrar::class,
		],
		Connector\Services\ConnectorRegistrar::class => [
			'container' => ContainerInterface::class,
			'messageBus' => MessageBus::class,
		],
	];

	public const LISTENERS = [
		Connector\Services\AuthRequestService::class,
		Connector\Services\ChannelRefresher::class,
		Connector\Services\ConnectionRefresher::class,
	];
}
