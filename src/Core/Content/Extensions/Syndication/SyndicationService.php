<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Handle Syndication commands.
 */
class SyndicationService implements Listener {
	/**
	 * Construct the service
	 *
	 * @param MessageBus $bus For dispatching Events.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Handle the AddSyndicationLink command.
	 *
	 * @param AddSyndicationLink $command Command to execute.
	 * @return void
	 */
	public function onAddSyndicationLink(AddSyndicationLink $command) {
		$this->bus->dispatch(new ContentSyndicated(
			url: $command->url,
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		));
	}

	/**
	 * Handle the SetSyndicationChannels command
	 *
	 * @param SetSyndicationChannels $command Command to execute.
	 * @return void
	 */
	public function onSetSyndicationChannels(SetSyndicationChannels $command) {
		$this->bus->dispatch(new SyndicationChannelsSet(
			channels: $command->channels,
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		));
	}
}
