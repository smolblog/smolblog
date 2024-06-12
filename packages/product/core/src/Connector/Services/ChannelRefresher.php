<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Core\Connector\Commands\RefreshChannels;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\ChannelDeleted;
use Smolblog\Core\Connector\Events\ChannelSaved;
use Smolblog\Core\Connector\Events\ConnectionEstablished;
use Smolblog\Core\Connector\Queries\ChannelsForConnection;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Service to update Channels for a Connection based on a provider.
 */
class ChannelRefresher implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus        $messageBus MessageBus for the system.
	 * @param ConnectorRegistry $connectors ConnectorRegistry for retrieving the Connection's Connector.
	 */
	public function __construct(
		private MessageBus $messageBus,
		private ConnectorRegistry $connectors,
	) {
	}

	/**
	 * Respond to the RefreshChannels command.
	 *
	 * @param RefreshChannels $command Command with ID of Connection to refresh.
	 * @return void
	 */
	public function onRefreshChannels(RefreshChannels $command): void {
		$connection = $this->messageBus->fetch(new ConnectionById(connectionId: $command->connectionId));
		$this->refresh(connection: $connection, userId: $command->userId);
	}

	/**
	 * Respond to the onConnectionEstablished event.
	 *
	 * When a Connection is established (or re-established), we should automatically refresh the channel listing. Do
	 * this after the projection is run.
	 *
	 * @param ConnectionEstablished $event Event with Connection that has been established.
	 * @return void
	 */
	#[ExecutionLayerListener(later: 5)]
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
		$connector = $this->connectors->get($connection->provider);

		$currentChannels = $this->messageBus->fetch(new ChannelsForConnection(connectionId: $connection->id));
		$newChannels = $connector->getChannels(connection: $connection);

		$toDeactivate = array_diff($currentChannels, $newChannels);
		foreach ($toDeactivate as $deleteMe) {
			$this->messageBus->dispatch(new ChannelDeleted(
				channelKey: $deleteMe->channelKey,
				connectionId: $connection->id,
				userId: $userId,
			));
		}

		foreach ($newChannels as $channel) {
			$this->messageBus->dispatch(new ChannelSaved(
				channelKey: $channel->channelKey,
				displayName: $channel->displayName,
				details: $channel->details,
				connectionId: $connection->id,
				userId: $userId,
			));
		}
	}
}
