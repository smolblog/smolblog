<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use Smolblog\Core\ContentV1\Events\PublicContentRemoved;

/**
 * Indicates a Note has been published.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class PublicNoteRemoved extends PublicContentRemoved implements NoteBuilder {
}
