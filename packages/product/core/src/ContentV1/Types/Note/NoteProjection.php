<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Foundation\Service\Messaging\ContentBuildLayerListener;
use Smolblog\Foundation\Service\Messaging\Projection;

/**
 * Store note-specific data.
 */
class NoteProjection implements Projection {
	public const TABLE = 'notes';

	/**
	 * Create the projection.
	 *
	 * @param ConnectionInterface $db Working database connection.
	 */
	public function __construct(
		private ConnectionInterface $db,
	) {
	}

	/**
	 * Add a note's data to the database.
	 *
	 * @param NoteCreated $event Event to handle.
	 * @return void
	 */
	public function onNoteCreated(NoteCreated $event) {
		$this->db->table(self::TABLE)->insert([
			'content_uuid' => $event->contentId->toString(),
			'markdown' => $event->text,
			'html' => $event->getNewBody(),
		]);
	}

	/**
	 * Update a note's data.
	 *
	 * @param NoteBodyEdited $event Event to handle.
	 * @return void
	 */
	public function onNoteBodyEdited(NoteBodyEdited $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'markdown' => $event->text,
			'html' => $event->getNewBody(),
		]);
	}

	/**
	 * Delete a note.
	 *
	 * @param NoteDeleted $event Event to handle.
	 * @return void
	 */
	public function onNoteDeleted(NoteDeleted $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->delete();
	}

	/**
	 * Add Note-specific data to a ContentBuilder.
	 *
	 * @param NoteBuilder $message Message that needs a Note.
	 * @return void
	 */
	#[ContentBuildLayerListener]
	public function buildNote(NoteBuilder $message) {
		$row = $this->db->table(self::TABLE)->where('content_uuid', '=', $message->getContentId()->toString())->first();

		$message->setContentType(new Note(
			text: $row->markdown,
			rendered: $row->html ?? null,
		));
	}
}
