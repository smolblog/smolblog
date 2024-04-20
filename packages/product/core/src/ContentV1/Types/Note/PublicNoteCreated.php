<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use Smolblog\Core\ContentV1\Events\PublicContentAdded;

/**
 * Indicates a Note has been published.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class PublicNoteCreated extends PublicContentAdded implements NoteBuilder {
}
