<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Core\Connector\Commands\RefreshChannels;
use Smolblog\Core\Connector\ConnectorRegistrar;
use Smolblog\Core\Connector\Entities\ChannelReader;
use Smolblog\Core\Connector\Entities\ChannelWriter;
use Smolblog\Core\Connector\Entities\ConnectionReader;
use Smolblog\Framework\Service;

/**
 * Service to update Channels for a Connection based on a provider.
 */
class ChannelRefresher implements Service {
	/**
	 * Construct the service.
	 *
	 * @param ConnectionReader   $connections   ConnectionReader for retrieving the given Connection.
	 * @param ConnectorRegistrar $connectors    ConnectorRegistrar for retrieving the Connection's Connector.
	 * @param ChannelReader      $channelReader ChannelReader for getting the existing Channels.
	 * @param ChannelWriter      $channelWriter ChannelWriter for saving and deleting Channels.
	 */
	public function __construct(
		private ConnectionReader $connections,
		private ConnectorRegistrar $connectors,
		private ChannelReader $channelReader,
		private ChannelWriter $channelWriter,
	) {
	}

	/**
	 * Update Channels for the given Connection based on the provider.
	 *
	 * @param RefreshChannels $command Command with ID of Connection to refresh.
	 * @return void
	 */
	public function run(RefreshChannels $command): void {
		$connection = $this->connections->get($command->connectionId);
		$connector = $this->connectors->get($connection->provider);

		$currentChannels = $this->channelReader->getChannelsForConnection(connectionId: $connection->id);
		$newChannels = $connector->getChannels(connection: $connection);

		$toDeactivate = array_diff($currentChannels, $newChannels);
		foreach ($toDeactivate as $deleteMe) {
			$this->channelWriter->delete($deleteMe->id);
		}

		foreach ($newChannels as $channel) {
			$this->channelWriter->save($channel);
		}
	}
}
