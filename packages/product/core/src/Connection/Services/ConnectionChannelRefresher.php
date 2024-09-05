<?php

namespace Smolblog\Core\Connection\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Connection\Commands\RefreshChannels;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\ConnectionEstablished;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;
use Smolblog\Foundation\Service\Event\EventListener;
use Smolblog\Foundation\Service\Event\EventListenerService;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Service to update Channels for a Connection based on a provider.
 */
class ConnectionChannelRefresher implements CommandHandlerService, EventListenerService {
	/**
	 * Construct the service
	 *
	 * @param ConnectionRepo            $connections For fetching Connections.
	 * @param ConnectionHandlerRegistry $handlers    For handling Connections.
	 * @param EventDispatcherInterface  $eventBus    For saving the updated Connection.
	 */
	public function __construct(
		private ConnectionRepo $connections,
		private ConnectionHandlerRegistry $handlers,
		private EventDispatcherInterface $eventBus,
	) {
	}

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
	 * Update Channels for the given Connection based on the provider.
	 *
	 * @param Connection $connection Connection to refresh.
	 * @param Identifier $userId     ID of User instigating this change.
	 * @return void
	 */
	public function refresh(Connection $connection, Identifier $userId): void {
		/*
			$connector = $this->handlers->get($connection->provider);

			$currentChannels = $this->messageBus->fetch(new ChannelsForConnection(connectionId: $connection->id));
			$newChannels = $connector->getChannels(connection: $connection);

			$toDeactivate = array_diff($currentChannels, $newChannels);
			foreach ($toDeactivate as $deleteMe) {
			$this->messageBus->dispatch(new ChannelDeleted(
				channelKey: $deleteMe->channelKey,
				connectionId: $connection->getId(),
				userId: $userId,
			));
			}

			foreach ($newChannels as $channel) {
			$this->messageBus->dispatch(new ChannelSaved(
				channelKey: $channel->channelKey,
				displayName: $channel->displayName,
				details: $channel->details,
				connectionId: $connection->getId(),
				userId: $userId,
			));
			}
		*/
	}
}
