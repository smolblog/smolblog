<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Content\ContentBuilder;

/**
 * Indicates that this is a ContentBuilder message that expectes a Note.
 *
 * Having this be a separate interface allows the NoteProjection to selectively listen for events.
 */
interface NoteBuilder extends ContentBuilder {
}
