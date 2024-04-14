<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use DateTimeImmutable;
use Smolblog\Core\ContentV1\ContentTypeConfiguration;
use Smolblog\Core\ContentV1\ContentTypeService;
use Smolblog\Core\ContentV1\ContentUtilityKit;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

/**
 * Service to handle Note-related commands.
 */
class NoteService implements Listener, ContentTypeService {
	use ContentUtilityKit;

	/**
	 * Get the Note configuration.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			handle: 'note',
			displayName: 'Note',
			typeClass: Note::class,
			singleItemQuery: NoteById::class,
			deleteItemCommand: DeleteNote::class,
		);
	}

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
	 * Create a Note.
	 *
	 * @param CreateNote $command Command information.
	 * @return void
	 */
	public function onCreateNote(CreateNote $command) {
		$this->bus->dispatch(new NoteCreated(
			text: $command->text,
			authorId: $command->userId,
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			publishTimestamp: $command->publish ? new DateTimeImmutable() : null,
		));

		if ($command->publish) {
			$this->bus->dispatch(new PublicNoteCreated(
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
			));
		}
	}

	/**
	 * Edit a note
	 *
	 * @param EditNote $command Command information.
	 * @return void
	 */
	public function onEditNote(EditNote $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->dispatch(new NoteBodyEdited(
			...$contentParams,
			text: $command->text,
		));

		$this->dispatchIfContentPublic(
			new PublicNoteEdited(...$contentParams),
			$contentParams,
		);
	}

	/**
	 * Publish a draft note
	 *
	 * @param PublishNote $command Command to execute.
	 * @return void
	 */
	public function onPublishNote(PublishNote $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$note = $this->bus->fetch(new NoteById(...$contentParams));

		if ($note->visibility !== ContentVisibility::Published) {
			$this->bus->dispatch(new PublicNoteCreated(...$contentParams));
		}
	}

	/**
	 * Delete a note
	 *
	 * @param DeleteNote $command Command information.
	 * @return void
	 */
	public function onDeleteNote(DeleteNote $command) {
		$contentParams = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->dispatchIfContentPublic(
			new PublicNoteRemoved(...$contentParams),
			$contentParams,
		);

		$this->bus->dispatch(new NoteDeleted(...$contentParams));
	}
}
