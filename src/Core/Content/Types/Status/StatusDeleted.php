<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Content\Events\ContentDeleted;

/**
 * Indicates that a Status has been deleted.
 *
 * There is no extra information attached to this event, but it is its own event so it can be picked up by the
 * StatusProjection (without it having to listen to *every* ContentDeleted event.)
 */
class StatusDeleted extends ContentDeleted {
}
