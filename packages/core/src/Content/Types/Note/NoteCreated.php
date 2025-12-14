<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Events\ContentCreated;

/**
 * Event noting that a Note specifically has been created.
 */
readonly class NoteCreated extends ContentCreated {
}
