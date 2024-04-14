<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\Events\PublicContentRemoved;

/**
 * Indicates a Picture has been published.
 */
readonly class PublicPictureRemoved extends PublicContentRemoved implements PictureBuilder {
}
