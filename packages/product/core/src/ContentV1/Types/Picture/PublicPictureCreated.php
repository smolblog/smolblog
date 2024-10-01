<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\Events\PublicContentAdded;

/**
 * Indicates a Picture has been published.
 */
class PublicPictureCreated extends PublicContentAdded implements PictureBuilder {
}
