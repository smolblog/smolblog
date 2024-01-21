<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\Events\PublicContentRemoved;

/**
 * Indicates a Picture has been published.
 */
class PublicPictureRemoved extends PublicContentRemoved implements PictureBuilder {
}
