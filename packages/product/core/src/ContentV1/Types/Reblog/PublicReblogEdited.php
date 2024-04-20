<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\ContentV1\Events\PublicContentChanged;

/**
 * Indicates a Reblog has been published.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class PublicReblogEdited extends PublicContentChanged implements ReblogBuilder {
}
