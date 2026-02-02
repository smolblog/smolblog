<?php

namespace Smolblog\Core\Channel\Services;

use Cavatappi\Foundation\Command\CommandHandler;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Channel\Commands\AddChannelToSite;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Events\ChannelAddedToSite;
use Smolblog\Core\Permissions\SitePermissionsService;

/**
 * Service to handle setting permissions for a Site and Channel.
 */
class ChannelLinker implements CommandHandlerService {
	/**
	 * Construct the service.
	 *
	 * @param EventDispatcherInterface $eventBus MesageBus to send events.
	 * @param ChannelRepo              $channels Get channels from storage.
	 * @param SitePermissionsService   $perms    Check site permissions.
	 */
	public function __construct(
		private EventDispatcherInterface $eventBus,
		private ChannelRepo $channels,
		private SitePermissionsService $perms,
	) {}

	/**
	 * Handle the command to set permissions.
	 *
	 * @throws EntityNotFound Thrown when the given channel does not exist.
	 * @throws CommandNotAuthorized Thrown when the user does not have correct permissions.
	 *
	 * @param AddChannelToSite $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onAddChannelToSite(AddChannelToSite $command): void {
		$channel = $this->channels->channelById($command->channelId);
		if (!isset($channel)) {
			throw new EntityNotFound(entityId: $command->channelId, entityName: Channel::class);
		}
		if (
			!(!isset($channel->userId) || $channel->userId->equals($command->userId))
			|| !($this->perms->canManageChannels(userId: $command->userId, siteId: $command->siteId))
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
