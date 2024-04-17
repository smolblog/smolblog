<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Core\Connector\Commands\LinkChannelToSite;
use Smolblog\Core\Connector\Events\ChannelSiteLinkSet;
use Smolblog\Core\Connector\Queries\ChannelById;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

/**
 * Service to handle setting permissions for a Site and Channel.
 */
class ChannelLinker implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus $bus MesageBus to send events.
	 */
	public function __construct(private MessageBus $bus) {
	}

	/**
	 * Handle the command to set permissions.
	 *
	 * @throws EntityNotFound Thrown when an ID is not found.
	 *
	 * @param LinkChannelToSite $command Command to execute.
	 * @return void
	 */
	public function onLinkChannelToSite(LinkChannelToSite $command): void {
		$channel = $this->bus->fetch(new ChannelById($command->channelId));
		if (!$channel) {
			throw new EntityNotFound(
				message: "Channel $command->channelId not found.",
			);
		}

		$this->bus->dispatch(new ChannelSiteLinkSet(
			channelId: $channel->getId(),
			siteId: $command->siteId,
			canPull: $command->canPull,
			canPush: $command->canPush,
			connectionId: $channel->connectionId,
			userId: $command->userId,
		));
	}
}
