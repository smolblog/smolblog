<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use Smolblog\Core\ContentV1\Events\PublicContentChanged;

/**
 * Indicates a Note has been published.
 */
class PublicNoteEdited extends PublicContentChanged implements NoteBuilder {
}