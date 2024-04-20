<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use Smolblog\Core\ContentV1\Events\ContentDeleted;

/**
 * Indicates that a Note has been deleted.
 *
 * There is no extra information attached to this event, but it is its own event so it can be picked up by the
 * NoteProjection (without it having to listen to *every* ContentDeleted event.)
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class NoteDeleted extends ContentDeleted {
}
