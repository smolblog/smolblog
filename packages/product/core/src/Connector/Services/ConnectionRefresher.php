<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\ConnectionRefreshed;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Core\User\User;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Service to check if a Connection needs a refresh and save the refreshed Connection if so.
 */
class ConnectionRefresher implements Listener {
	/**
	 * Construct the service
	 *
	 * @param ConnectorRegistry $connectorRepo Connectors to look up.
	 * @param MessageBus        $messageBus    MessageBus to send the save event.
	 */
	public function __construct(
		private ConnectorRegistry $connectorRepo,
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
		if ($query->results()) {
			$query->setResults($this->refresh($query->results(), userId: User::internalSystemUser()->id));
		}
	}

	/**
	 * Check the given Connection to see if it needs to be refreshed. If it does, refresh it and save the result.
	 *
	 * @param Connection $connection Connection object to check.
	 * @param Identifier $userId     User initiating the check.
	 * @return Connection Connection object ready to be used.
	 */
	public function refresh(Connection $connection, Identifier $userId): Connection {
		$connector = $this->connectorRepo->get($connection->provider);
		if (!$connector->connectionNeedsRefresh($connection)) {
			return $connection;
		}

		$refreshed = $connector->refreshConnection($connection);
		$this->messageBus->dispatch(new ConnectionRefreshed(
			details: $refreshed->details,
			connectionId: $refreshed->id,
			userId: $userId,
		));
		return $refreshed;
	}
}
