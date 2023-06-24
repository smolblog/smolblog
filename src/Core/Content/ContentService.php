<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Commands\EditContentBaseAttributes;
use Smolblog\Core\Content\Events\ContentBaseAttributeEdited;
use Smolblog\Core\Content\Queries\BaseContentById;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Handle generic content commands.
 */
class ContentService implements Listener {
	/**
	 * Construct the service
	 *
	 * @param MessageBus $bus MessageBus for messages.
	 */
	public function __construct(
		private MessageBus $bus,
	) {
	}

	/**
	 * Edit the base attributes on a piece of content.
	 *
	 * @param EditContentBaseAttributes $command Valid command to execute.
	 * @return void
	 */
	public function onEditContentBaseAttributes(EditContentBaseAttributes $command): void {
		$this->bus->dispatch(new ContentBaseAttributeEdited(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			publishTimestamp: $command->publishTimestamp,
			authorId: $command->authorId,
		));
	}
}
