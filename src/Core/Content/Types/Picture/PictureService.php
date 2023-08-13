<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Handle Picture-related commands.
 */
class PictureService implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus $bus For dispatching events.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Create a picture.
	 *
	 * @param CreatePicture $command Command to execute.
	 * @return void
	 */
	public function onCreatePicture(CreatePicture $command) {
		$event = new PictureCreated(
			...$command->toArray(),
			authorId: $command->userId,
		);

		$this->bus->dispatch($event);
	}

	/**
	 * Publish a picture
	 *
	 * @param PublishPicture $command Command to execute.
	 * @return void
	 */
	public function onPublishPicture(PublishPicture $command) {
		$note = $this->bus->fetch(new PictureById(...$command->toArray()));

		if ($note->visibility !== ContentVisibility::Published) {
			$this->bus->dispatch(new PublicPictureCreated(...$command->toArray()));
		}
	}

	/**
	 * Delete a note
	 *
	 * @param DeletePicture $command Command information.
	 * @return void
	 */
	public function onDeletePicture(DeletePicture $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$note = $this->bus->fetch(new PictureById(...$contentParams));

		if ($note->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicPictureRemoved(...$contentParams));
		}

		$this->bus->dispatch(new PictureDeleted(...$contentParams));
	}
}
