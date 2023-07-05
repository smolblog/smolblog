<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Events\PublicContentRemoved;

/**
 * Indicates a Note has been published.
 */
class PublicNoteRemoved extends PublicContentRemoved implements NoteBuilder {
}
