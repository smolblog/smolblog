<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\Events\PublicContentChanged;

/**
 * Indicates a Picture has been published.
 */
readonly class PublicPictureEdited extends PublicContentChanged implements PictureBuilder {
}
