<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Handle tasks related to Media.
 */
class MediaService implements Listener {
	/**
	 * Create the service.
	 *
	 * @param MessageBus           $bus      MessageBus to dispatch events.
	 * @param MediaHandlerRegistry $registry Available MediaHandlers.
	 */
	public function __construct(
		private MessageBus $bus,
		private MediaHandlerRegistry $registry,
	) {
	}

	/**
	 * Handle the HandleUploadMedia command.
	 *
	 * @param HandleUploadedMedia $command Command to execute.
	 * @return void
	 */
	public function onHandleUploadedMedia(HandleUploadedMedia $command) {
		$handler = $this->registry->get();
		$newMedia = $handler->handleUploadedFile(
			file: $command->file,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$this->bus->dispatch(new MediaAdded(
			contentId: $newMedia->id,
			userId: $newMedia->userId,
			siteId: $newMedia->siteId,
			title: $newMedia->title,
			accessibilityText: $newMedia->accessibilityText,
			type: $newMedia->type,
			handler: $newMedia->handler,
			attribution: $newMedia->attribution,
			info: $newMedia->info,
		));
		$command->createdMedia = $newMedia;
	}
}
