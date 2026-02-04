<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Entities\ContentTypeConfiguration;
use Smolblog\Core\Content\Services\DefaultContentTypeService;

/**
 * ContentTypeService to handle Notes.
 */
class NoteService extends DefaultContentTypeService {
	/**
	 * Get the configuration for the Note content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			key: Note::getKey(),
			displayName: 'Note',
			typeClass: Note::class,
		);
	}

	protected const CREATE_EVENT = NoteCreated::class;
	protected const UPDATE_EVENT = NoteUpdated::class;
	protected const DELETE_EVENT = NoteDeleted::class;
}
