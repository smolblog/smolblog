<?php

namespace Smolblog\Core\Connection\Services;

use Cavatappi\Foundation\Command\CommandHandler;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Cavatappi\Foundation\Service;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Connection\Commands\RefreshConnection;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\ConnectionRefreshed;

/**
 * Service to check if a Connection needs a refresh and save the refreshed Connection if so.
 */
class ConnectionRefresher implements CommandHandlerService {
	/**
	 * Create the service
	 *
	 * @param ConnectionRepo            $connections For fetching Connections.
	 * @param ConnectionHandlerRegistry $handlers    For handling Connections.
	 * @param EventDispatcherInterface  $eventBus    For saving the updated Connection.
	 */
	public function __construct(
		private ConnectionRepo $connections,
		private ConnectionHandlerRegistry $handlers,
		private EventDispatcherInterface $eventBus,
	) {}

	/**
	 * Handle the RefreshConnection command
	 *
	 * @throws EntityNotFound When the given Connection cannot be found.
	 *
	 * @param RefreshConnection $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onRefreshConnection(RefreshConnection $command) {
		$connection = $this->connections->connectionById($command->connectionId);
		if (!isset($connection)) {
			throw new EntityNotFound($command->connectionId, Connection::class);
		}

		$this->refresh(connection: $connection, userId: $command->userId);
	}

	/**
	 * Check the given Connection to see if it needs to be refreshed. If it does, refresh it and save the result.
	 *
	 * @param Connection    $connection Connection object to check.
	 * @param UuidInterface $userId     User initiating the check.
	 * @return Connection Connection object ready to be used.
	 */
	public function refresh(Connection $connection, UuidInterface $userId): Connection {
		$connector = $this->handlers->get($connection->handler);
		if (!$connector->connectionNeedsRefresh($connection)) {
			return $connection;
		}

		$refreshed = $connector->refreshConnection($connection);
		$this->eventBus->dispatch(new ConnectionRefreshed(
			details: $refreshed->details,
			entityId: $refreshed->id,
			userId: $userId,
		));
		return $refreshed;
	}
}
