<?php

namespace Smolblog\Mock;

use PDO;
use Smolblog\Core\Connector\Services\AuthRequestStateRepo;
use Smolblog\Framework\Objects\DomainModel;

/**
 * Model for the mock app's classes.
 */
class Model extends DomainModel {
	public const SERVICES = [
		EventStreams\ConnectorEventStream::class => ['db' => PDO::class],
		EventStreams\ContentEventStream::class => ['db' => PDO::class],
		Projections\ConnectionProjection::class => ['db' => PDO::class],
		Projections\ChannelProjection::class => ['db' => PDO::class],
		Projections\StandardContentProjection::class => ['db' => PDO::class],
		Projections\NoteProjection::class => ['db' => PDO::class],
		Transients::class => ['db' => PDO::class],
		SecurityService::class => ['db' => PDO::class],

		AuthRequestStateRepo::class => Transients::class,
	];

	public const LISTENERS = [
		EventStreams\ConnectorEventStream::class,
		EventStreams\ContentEventStream::class,
		Projections\ConnectionProjection::class,
		Projections\ChannelProjection::class,
		Projections\StandardContentProjection::class,
		Projections\NoteProjection::class,
		SecurityService::class,
	];
}
