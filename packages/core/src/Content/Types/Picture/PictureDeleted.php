<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\Events\ContentDeleted;

/**
 * Event noting that a Picture specifically has been deleted.
 */
readonly class PictureDeleted extends ContentDeleted {
}
