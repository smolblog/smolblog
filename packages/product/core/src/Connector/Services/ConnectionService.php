<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Core\Connector\Commands\DeleteConnection;
use Smolblog\Core\Connector\Events\ConnectionDeleted;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Service for basic Connection operations.
 */
class ConnectionService implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus $bus MessageBus for dispatching events.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Handle the DeleteConnection command and delete a connection.
	 *
	 * @param DeleteConnection $command Command to execute.
	 * @return void
	 */
	public function onDeleteConnection(DeleteConnection $command) {
		$this->bus->dispatch(new ConnectionDeleted(
			connectionId: $command->connectionId,
			userId: $command->userId,
		));
	}
}
