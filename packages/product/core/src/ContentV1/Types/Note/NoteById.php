<?php

namespace Smolblog\Core\ContentV1\Types\Note;

use Smolblog\Core\ContentV1\Queries\BaseContentById;

/**
 * Get a Note by its id.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class NoteById extends BaseContentById implements NoteBuilder {
}
