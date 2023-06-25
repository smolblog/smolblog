<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\Events\ContentDeleted;

/**
 * Indicates that a Note has been deleted.
 *
 * There is no extra information attached to this event, but it is its own event so it can be picked up by the
 * NoteProjection (without it having to listen to *every* ContentDeleted event.)
 */
class NoteDeleted extends ContentDeleted {
}
