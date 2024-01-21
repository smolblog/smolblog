<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Events\PublicContentChanged;

/**
 * Indicates a Note has been published.
 */
class PublicNoteEdited extends PublicContentChanged implements NoteBuilder {
}
