<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\Events\ContentDeleted;

/**
 * Indicates that a Picture has been deleted.
 *
 * There is no extra information attached to this event, but it is its own event so it can be picked up by the
 * PictureProjection (without it having to listen to *every* ContentDeleted event.)
 */
class PictureDeleted extends ContentDeleted {
}
