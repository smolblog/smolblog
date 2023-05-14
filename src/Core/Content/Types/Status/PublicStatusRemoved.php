<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Content\Events\PublicContentRemoved;

/**
 * Indicates a Status has been published.
 */
class PublicStatusRemoved extends PublicContentRemoved implements StatusBuilder {
}
