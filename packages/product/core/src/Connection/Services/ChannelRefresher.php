<?php

namespace Smolblog\Core\Connection\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Connection\Commands\RefreshChannels;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Events\ChannelDeleted;
use Smolblog\Core\Connection\Events\ChannelSaved;
use Smolblog\Core\Connection\Events\ConnectionEstablished;
use Smolblog\Core\Connection\Queries\ChannelsForConnection;
use Smolblog\Core\Connection\Queries\ConnectionById;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;
use Smolblog\Foundation\Service\Event\EventListener;
use Smolblog\Foundation\Service\Event\EventListenerService;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Service to update Channels for a Connection based on a provider.
 */
class ChannelRefresher implements CommandHandlerService, EventListenerService {
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
	 * Respond to the ConnectionEstablished event.
	 *
	 * When a Connection is established (or re-established), we should automatically refresh the channel listing. Do
	 * this after the projection is run.
	 *
	 * @param ConnectionEstablished $event Event with Connection that has been established.
	 * @return void
	 */
	#[EventListener]
	public function onConnectionEstablished(ConnectionEstablished $event): void {
		$connection = new Connection(
			userId: $event->userId,
			provider: $event->provider,
			providerKey: $event->providerKey,
			displayName: $event->displayName,
			details: $event->details,
		);
		$this->refresh(connection: $connection, userId: $event->userId);
	}

	/**
	 * Update Channels for the given Connection based on the provider.
	 *
	 * @param Connection $connection Connection to refresh.
	 * @param Identifier $userId     ID of User instigating this change.
	 * @return void
	 */
	private function refresh(Connection $connection, Identifier $userId): void {
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
