<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Content\ContentBuilder;

/**
 * Indicates that this is a ContentBuilder message that expectes a Status.
 *
 * Having this be a separate interface allows the StatusProjection to selectively listen for events.
 */
interface StatusBuilder extends ContentBuilder {
}
