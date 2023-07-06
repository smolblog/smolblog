<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DateIdentifier;

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
		$mediaInfo = $handler->handleUploadedFile(
			file: $command->file,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$newMedia = new Media(
			id: new DateIdentifier(),
			userId: $command->userId,
			siteId: $command->siteId,
			title: $command->title ?? $command->file->getClientFilename() ?? $mediaInfo->type->name,
			accessibilityText: $command->accessibilityText ?? 'Uploaded file',
			type: $mediaInfo->type,
			handler: $mediaInfo->handler,
			attribution: $command->attribution,
			info: $mediaInfo->info,
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
		$command->urlToOriginal = $handler->getUrlFor($newMedia);
	}
}
