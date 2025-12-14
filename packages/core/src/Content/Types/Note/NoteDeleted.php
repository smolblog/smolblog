<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Events\ContentDeleted;

/**
 * Event noting that a Note specifically has been deleted.
 */
readonly class NoteDeleted extends ContentDeleted {
}
