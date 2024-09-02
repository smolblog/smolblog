<?php

namespace Smolblog\CoreDataSql;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\DomainModel;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model extends DomainModel {
	public const SERVICES = [
		Connection\ChannelProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Connection\ChannelSiteLinkProjection::class => [
			'db' => ConnectionInterface::class,
			'bus' => MessageBus::class,
		],
		Connection\ConnectionProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Connection\ConnectorEventStream::class => [
			'db' => ConnectionInterface::class,
		],
	];
}
