<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use Smolblog\Core\ContentV1\ContentBuilder;

/**
 * Indicates that this is a ContentBuilder message that expectes a Note.
 *
 * Having this be a separate interface allows the NoteProjection to selectively listen for events.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
interface NoteBuilder extends ContentBuilder {
}
