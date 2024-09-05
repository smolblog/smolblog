<?php

namespace Smolblog\Core\Connector\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Connector\Commands\DeleteConnection;
use Smolblog\Core\Connector\Data\ConnectionRepo;
use Smolblog\Core\Connector\Events\ConnectionDeleted;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;

/**
 * Service for basic Connection operations.
 *
 * The majority of operations are in separate services since they require more specialized dependencies that aren't
 * needed for the delete operation.
 */
class ConnectionService implements CommandHandlerService {
	/**
	 * Construct the service
	 *
	 * @param ConnectionRepo           $connections For checking connections.
	 * @param EventDispatcherInterface $eventBus    For dispatching completed events.
	 */
	public function __construct(
		private ConnectionRepo $connections,
		private EventDispatcherInterface $eventBus
	) {
	}

	/**
	 * Handle the DeleteConnection command and delete a connection.
	 *
	 * @throws CommandNotAuthorized If the user does not own the connection.
	 *
	 * @param DeleteConnection $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onDeleteConnection(DeleteConnection $command) {
		if (
			!$this->connections->connectionBelongsToUser(connectionId: $command->connectionId, userId: $command->userId)
		) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		$this->eventBus->dispatch(new ConnectionDeleted(
			entityId: $command->connectionId,
			userId: $command->userId,
		));
	}
}
