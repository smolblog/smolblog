<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Events\ContentUpdated;

/**
 * Event noting that a Note specifically has been updated.
 */
readonly class NoteUpdated extends ContentUpdated {
}
