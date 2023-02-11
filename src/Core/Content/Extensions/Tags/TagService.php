<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Framework\Messages\MessageBus;

/**
 * Handle Tag commands.
 */
class TagService {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus $bus MessageBus instance for sending events.
	 */
	public function __construct(
		private MessageBus $bus,
	) {
	}

	/**
	 * Handle the SetTags command.
	 *
	 * @param SetTags $command Command to set tags on a piece of content.
	 * @return void
	 */
	public function onSetTags(SetTags $command): void {
		$this->bus->dispatch(new TagsSet(
			tagText: $command->tags,
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		));
	}
}
