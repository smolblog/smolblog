<?php

namespace Smolblog\Core\Channel\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Channel\Commands\AddChannelToSite;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Channel\Events\ChannelAddedToSite;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;

/**
 * Service to handle setting permissions for a Site and Channel.
 */
class ChannelLinker implements CommandHandlerService {
	/**
	 * Construct the service.
	 *
	 * @param EventDispatcherInterface $eventBus MesageBus to send events.
	 * @param ChannelRepo              $channels Get channels from storage.
	 */
	public function __construct(
		private EventDispatcherInterface $eventBus,
		private ChannelRepo $channels,
	) {
	}

	/**
	 * Handle the command to set permissions.
	 *
	 * @throws CommandNotAuthorized Thrown when the user does not have correct permissions.
	 *
	 * @param AddChannelToSite $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onAddChannelToSite(AddChannelToSite $command): void {
		if (
			!$this->channels->userCanLinkChannelAndSite(
				userId: $command->userId,
				channelId: $command->channelId,
				siteId: $command->siteId,
			)
		) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		$this->eventBus->dispatch(new ChannelAddedToSite(
			aggregateId: $command->siteId,
			entityId: $command->channelId,
			userId: $command->userId,
		));
	}
}
