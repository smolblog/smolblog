<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Events\ContentDeleted;

/**
 * Event noting that a Note specifically has been deleted.
 */
class NoteDeleted extends ContentDeleted {}
