<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Core\Connector\ConnectorRegistrar;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\ConnectionRefreshed;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Framework\MessageBus\MessageBus;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Objects\Identifier;

/**
 * Service to check if a Connection needs a refresh and save the refreshed Connection if so.
 */
class ConnectionRefresher {
	/**
	 * Construct the service
	 *
	 * @param ConnectorRegistrar $connectorRepo Connectors to look up.
	 * @param MessageBus         $messageBus    MessageBus to send the save event.
	 */
	public function __construct(
		private ConnectorRegistrar $connectorRepo,
		private MessageBus $messageBus,
	) {
	}

	/**
	 * Intercept the ConenctionById query and check the Connection is good.
	 *
	 * @param ConnectionById $query Query to intercept.
	 * @return void
	 */
	#[ExecutionLayerListener(later: 1)]
	public function checkOnConnectionById(ConnectionById $query) {
		$query->results = $this->refresh($query->results);
	}

	/**
	 * Check the given Connection to see if it needs to be refreshed. If it does, refresh it and save the result.
	 *
	 * @param Connection $connection Connection object to check.
	 * @return Connection Connection object ready to be used.
	 */
	public function refresh(Connection $connection): Connection {
		$connector = $this->connectorRepo->get($connection->provider);
		if (!$connector->connectionNeedsRefresh($connection)) {
			return $connection;
		}

		$refreshed = $connector->refreshConnection($connection);
		$this->messageBus->dispatch(new ConnectionRefreshed(
			details: $refreshed->details,
			connectionId: $refreshed->id,
			// TODO: replace with system account ID.
			userId: Identifier::fromString('e3f38a3e-eb0f-48f2-8803-6892a87ed20c'),
		));
		return $refreshed;
	}
}
