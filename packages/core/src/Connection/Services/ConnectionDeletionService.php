<?php

namespace Smolblog\Core\Connection\Services;

use Cavatappi\Foundation\Command\CommandHandler;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Connection\Commands\DeleteConnection;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Events\ConnectionDeleted;

/**
 * Service for basic Connection operations.
 *
 * The majority of operations are in separate services since they require more specialized dependencies that aren't
 * needed for the delete operation.
 */
class ConnectionDeletionService implements CommandHandlerService {
	/**
	 * Construct the service
	 *
	 * @param ConnectionRepo           $connections For checking connections.
	 * @param EventDispatcherInterface $eventBus    For dispatching completed events.
	 */
	public function __construct(
		private ConnectionRepo $connections,
		private EventDispatcherInterface $eventBus,
	) {}

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
