<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\ContentBuilder;

/**
 * Indicates that this is a ContentBuilder message that expectes a Picture.
 *
 * Having this be a separate interface allows the PictureProjection to selectively listen for events.
 */
interface PictureBuilder extends ContentBuilder {
}
