<?php

namespace Smolblog\Mock;

use PDO;
use Smolblog\Framework\Objects\DomainModel;

/**
 * Model for the mock app's classes.
 */
class Model extends DomainModel {
	public const SERVICES = [
		EventStreams\ConnectorEventStream::class => ['db' => PDO::class],
		Projections\ConnectionProjection::class => ['db' => PDO::class],
		Projections\ChannelProjection::class => ['db' => PDO::class],
	];

	public const LISTENERS = [
		EventStreams\ConnectorEventStream::class,
		Projections\ConnectionProjection::class,
		Projections\ChannelProjection::class,
	];
}
