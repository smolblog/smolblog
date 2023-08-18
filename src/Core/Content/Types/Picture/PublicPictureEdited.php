<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\Events\PublicContentChanged;

/**
 * Indicates a Picture has been published.
 */
class PublicPictureEdited extends PublicContentChanged implements PictureBuilder {
}
