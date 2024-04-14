<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\ContentTypeConfiguration;
use Smolblog\Core\ContentV1\ContentTypeService;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

/**
 * Handle Picture-related commands.
 */
class PictureService implements Listener, ContentTypeService {
	/**
	 * Get configuration for the content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			handle: 'picture',
			displayName: 'Picture',
			typeClass: Picture::class,
			singleItemQuery: PictureById::class,
			deleteItemCommand: DeletePicture::class,
		);
	}

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
	 * Update a picture's media.
	 *
	 * @param EditPictureMedia $command Command to execute.
	 * @return void
	 */
	public function onEditPictureMedia(EditPictureMedia $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$picture = $this->bus->fetch(new PictureById(...$contentParams));

		$this->bus->dispatch(new PictureMediaEdited(
			...$command->toArray(),
		));

		if ($picture->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicPictureEdited(...$contentParams));
		}
	}

	/**
	 * Update a picture's caption.
	 *
	 * @param EditPictureCaption $command Command to execute.
	 * @return void
	 */
	public function onEditPictureCaption(EditPictureCaption $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$picture = $this->bus->fetch(new PictureById(...$contentParams));

		$this->bus->dispatch(new PictureCaptionEdited(
			...$command->toArray(),
		));

		if ($picture->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicPictureEdited(...$contentParams));
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
