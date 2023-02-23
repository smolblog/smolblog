<?php

namespace Smolblog\Core\Content\Extensions\SyndicationLinks;

use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Handle SyndicationLink commands.
 */
class SyndicationLinkService implements Listener {
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
}
