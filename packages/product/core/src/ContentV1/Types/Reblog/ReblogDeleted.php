<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\ContentV1\Events\ContentDeleted;

/**
 * Indicates that a Reblog has been deleted.
 *
 * There is no extra information attached to this event, but it is its own event so it can be picked up by the
 * ReblogProjection (without it having to listen to *every* ContentDeleted event.)
 */
readonly class ReblogDeleted extends ContentDeleted {
}
