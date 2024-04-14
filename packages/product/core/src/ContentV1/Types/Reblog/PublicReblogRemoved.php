<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\ContentV1\Events\PublicContentRemoved;

/**
 * Indicates a Reblog has been published.
 */
readonly class PublicReblogRemoved extends PublicContentRemoved implements ReblogBuilder {
}
