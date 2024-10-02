<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\Events\ContentDeleted;

/**
 * Event noting that a Reblog specifically has been deleted.
 */
readonly class ReblogDeleted extends ContentDeleted {
}
