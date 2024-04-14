<?php

namespace Smolblog\Core\Syndication;

use Smolblog\Core\Connector\Queries\ChannelById;
use Smolblog\Core\Connector\Queries\ChannelsForAdmin;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Core\Connector\Services\ConnectorRegistry;
use Smolblog\Core\ContentV1\Events\PublicContentAdded;
use Smolblog\Core\ContentV1\Extensions\Syndication\Syndication;
use Smolblog\Core\User\User;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Handle pushing content to external services.
 */
class SyndicationService implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus        $bus        Sending messages.
	 * @param ConnectorRegistry $connectors Get Connector objects.
	 */
	public function __construct(
		private MessageBus $bus,
		private ConnectorRegistry $connectors,
	) {
	}

	/**
	 * Get newly published content and push it to the given channels.
	 *
	 * @param PublicContentAdded $event Event to handle.
	 * @return void
	 */
	public function onPublicContentAdded(PublicContentAdded $event): void {
		$content = $event->getContent();
		$channelIds = $content->extensions[Syndication::class]?->channels ?? [];

		foreach ($channelIds as $channelId) {
			$channel = $this->bus->fetch(new ChannelById($channelId));
			$connection = $this->bus->fetch(new ConnectionById($channel->connectionId));

			$this->bus->dispatchAsync(new PushContentToChannel(
				content: $content,
				channel: $channel,
				connection: $connection,
			));
		}
	}

	/**
	 * Handle the async command and give its info to the appropriate Connector.
	 *
	 * @param PushContentToChannel $command Command to execute.
	 * @return void
	 */
	public function onPushContentToChannel(PushContentToChannel $command): void {
		$connector = $this->connectors->get($command->connection->provider);

		$connector->push(
			content: $command->content,
			toChannel: $command->channel,
			withConnection: $command->connection
		);
	}
}
