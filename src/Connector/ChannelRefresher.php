<?php

namespace Smolblog\Core\Connector;

/**
 * Service to update Channels for a Connection based on a provider.
 */
class ChannelRefresher {
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
	public function handleRefreshChannels(RefreshChannels $command): void {
		$connection = $this->connections->get($command->connectionId);
		$connector = $this->connectors->get($connection->provider);

		$currentChannels = $this->channelReader->getChannelsFor(connection: $connection);
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
