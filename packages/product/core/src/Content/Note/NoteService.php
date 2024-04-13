<?php

namespace Smolblog\Core\Content\Note;

use Smolblog\Core\Content\Note;
use Smolblog\Core\Content\Type\ContentTypeConfiguration;
use Smolblog\Core\Content\Type\DefaultContentTypeService;

class NoteService extends DefaultContentTypeService {
	public static function getConfiguration(): ContentTypeConfiguration
	{
		return new ContentTypeConfiguration(
			key: Note::KEY,
			displayName: 'Note',
			typeClass: Note::class,
		);
	}
}
