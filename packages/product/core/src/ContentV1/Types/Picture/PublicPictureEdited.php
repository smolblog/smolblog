<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\Events\PublicContentChanged;

/**
 * Indicates a Picture has been published.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class PublicPictureEdited extends PublicContentChanged implements PictureBuilder {
}
