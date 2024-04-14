<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\ContentV1\Events\PublicContentChanged;

/**
 * Indicates a Reblog has been published.
 */
readonly class PublicReblogEdited extends PublicContentChanged implements ReblogBuilder {
}
