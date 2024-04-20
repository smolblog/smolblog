<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\ContentV1\Events\PublicContentRemoved;

/**
 * Indicates a Reblog has been published.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class PublicReblogRemoved extends PublicContentRemoved implements ReblogBuilder {
}
