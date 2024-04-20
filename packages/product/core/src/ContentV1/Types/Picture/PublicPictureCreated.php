<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\Events\PublicContentAdded;

/**
 * Indicates a Picture has been published.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class PublicPictureCreated extends PublicContentAdded implements PictureBuilder {
}
