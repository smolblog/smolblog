<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\Events\PublicContentRemoved;

/**
 * Indicates a Picture has been published.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class PublicPictureRemoved extends PublicContentRemoved implements PictureBuilder {
}
