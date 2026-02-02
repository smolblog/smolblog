<?php

namespace Smolblog\Core\Connection\Services;

use Cavatappi\Foundation\Command\CommandHandler;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\DomainEvent\EventListenerService;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Channel\Events\ChannelDeleted;
use Smolblog\Core\Channel\Events\ChannelSaved;
use Smolblog\Core\Connection\Commands\RefreshChannels;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Entities\Connection;

/**
 * Service to update Channels for a Connection based on a handler.
 */
class ConnectionChannelRefresher implements CommandHandlerService, EventListenerService {
	/**
	 * Construct the service
	 *
	 * @param ConnectionRepo            $connections For fetching Connections.
	 * @param ChannelRepo               $channels    For getting the current Channels.
	 * @param ConnectionHandlerRegistry $handlers    For handling Connections.
	 * @param EventDispatcherInterface  $eventBus    For saving the updated Connection.
	 */
	public function __construct(
		private ConnectionRepo $connections,
		private ChannelRepo $channels,
		private ConnectionHandlerRegistry $handlers,
		private EventDispatcherInterface $eventBus,
	) {}

	/**
	 * Respond to the RefreshChannels command.
	 *
	 * @throws EntityNotFound When the given Connection cannot be found.
	 *
	 * @param RefreshChannels $command Command with ID of Connection to refresh.
	 * @return void
	 */
	#[CommandHandler]
	public function onRefreshChannels(RefreshChannels $command): void {
		$connection = $this->connections->connectionById(connectionId: $command->connectionId);
		if (!isset($connection)) {
			throw new EntityNotFound($command->connectionId, Connection::class);
		}

		$this->refresh(connection: $connection, userId: $command->userId);
	}

	/**
	 * Update Channels for the given Connection based on the handler.
	 *
	 * @param Connection    $connection Connection to refresh.
	 * @param UuidInterface $userId     ID of User instigating this change.
	 * @return void
	 */
	public function refresh(Connection $connection, UuidInterface $userId): void {
		$connector = $this->handlers->get($connection->handler);

		$currentChannels = $this->channels->channelsForConnection(connectionId: $connection->id);
		$newChannels = $connector->getChannels(connection: $connection);

		// $toDeactivate = array_diff($currentChannels, $newChannels);
		$toDeactivate = \array_filter($currentChannels, fn($channel) => !\in_array($channel, $newChannels));
		foreach ($toDeactivate as $deleteMe) {
			$this->eventBus->dispatch(new ChannelDeleted(
				entityId: $deleteMe->id,
				userId: $userId,
			));
		}

		// Saving all channels in case some details have changed.
		foreach ($newChannels as $channel) {
			$this->eventBus->dispatch(new ChannelSaved(
				channel: $channel,
				userId: $userId,
			));
		}
	}
}
