<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\ContentBuilder;

/**
 * Indicates that this is a ContentBuilder message that expectes a Reblog.
 *
 * Having this be a separate interface allows the ReblogProjection to selectively listen for events.
 */
interface ReblogBuilder extends ContentBuilder {
}
