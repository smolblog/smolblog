<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Content\Events\PublicContentAdded;

/**
 * Indicates a Status has been published.
 */
class PublicStatusCreated extends PublicContentAdded implements StatusBuilder {
}
