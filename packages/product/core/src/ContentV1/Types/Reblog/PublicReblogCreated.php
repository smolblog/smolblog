<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\ContentV1\Events\PublicContentAdded;

/**
 * Indicates a Reblog has been published.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class PublicReblogCreated extends PublicContentAdded implements ReblogBuilder {
}
