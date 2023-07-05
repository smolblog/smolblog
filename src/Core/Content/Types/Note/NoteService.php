<?php

namespace Smolblog\Core\Content\Types\Note;

use DateTimeImmutable;
use Smolblog\Core\Content\ContentTypeConfiguration;
use Smolblog\Core\Content\ContentTypeService;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\Identifier;

/**
 * Service to handle Note-related commands.
 */
class NoteService implements Listener, ContentTypeService {
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
		$id = new DateIdentifier();
		$this->bus->dispatch(new NoteCreated(
			text: $command->text,
			authorId: $command->userId,
			contentId: $id,
			userId: $command->userId,
			siteId: $command->siteId,
			publishTimestamp: new DateTimeImmutable(),
		));

		if ($command->publish) {
			$this->bus->dispatch(new PublicNoteCreated(
				contentId: $id,
				userId: $command->userId,
				siteId: $command->siteId,
			));
		}

		$command->noteId = $id;
	}

	/**
	 * Edit a note
	 *
	 * @param EditNote $command Command information.
	 * @return void
	 */
	public function onEditNote(EditNote $command) {
		$contentParams = [
			'contentId' => $command->noteId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$note = $this->bus->fetch(new NoteById(...$contentParams));

		$this->bus->dispatch(new NoteBodyEdited(
			...$contentParams,
			text: $command->text,
		));

		if ($note->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicNoteEdited(...$contentParams));
		}
	}

	/**
	 * Publish a draft note
	 *
	 * @param PublishNote $command Command to execute.
	 * @return void
	 */
	public function onPublishNote(PublishNote $command) {
		$contentParams = [
			'contentId' => $command->noteId,
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
			'contentId' => $command->noteId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$note = $this->bus->fetch(new NoteById(...$contentParams));

		if ($note->visibility === ContentVisibility::Published) {
			$this->bus->dispatch(new PublicNoteRemoved(...$contentParams));
		}

		$this->bus->dispatch(new NoteDeleted(...$contentParams));
	}
}
