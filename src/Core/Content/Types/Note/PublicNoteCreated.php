<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Events\PublicContentAdded;

/**
 * Indicates a Note has been published.
 */
class PublicNoteCreated extends PublicContentAdded implements NoteBuilder {
}
