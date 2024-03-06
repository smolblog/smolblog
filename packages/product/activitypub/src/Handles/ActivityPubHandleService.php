<?php

namespace Smolblog\ActivityPub\Handles;

use Smolblog\Core\Site\CreateSite;
use Smolblog\Core\Site\SiteById;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DateIdentifier;

/**
 * Service to handle ActivityPub handles for a site.
 */
class ActivityPubHandleService implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus $bus MessageBus for sending messages.
	 */
	public function __construct(
		private MessageBus $bus,
	) {
	}

	/**
	 * Handle the SetActivityPubHandle command.
	 *
	 * @param SetActivityPubHandle $command Command to handle.
	 * @return void
	 */
	public function onSetActivityPubHandle(SetActivityPubHandle $command): void {
		$this->bus->dispatch(new ActivityPubHandleCreated(
			handleId: $command->handleId,
			handle: $command->handle,
			siteId: $command->siteId,
			userId: $command->userId,
		));
	}

	/**
	 * Create a new ActivityPub handle for a site.
	 *
	 * @param CreateSite $command Command to handle.
	 * @return void
	 */
	public function onCreateSite(CreateSite $command) {
		$this->bus->dispatch(new ActivityPubHandleCreated(
			handleId: new DateIdentifier(),
			handle: $command->handle . '@smol.blog',
			siteId: $command->siteId,
			userId: $command->userId,
		));
	}
}
