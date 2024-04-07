<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\ContentV1\ContentBuilder;

/**
 * Indicates that this is a ContentBuilder message that expectes a Reblog.
 *
 * Having this be a separate interface allows the ReblogProjection to selectively listen for events.
 */
interface ReblogBuilder extends ContentBuilder {
}
