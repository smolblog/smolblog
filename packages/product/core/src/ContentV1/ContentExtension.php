<?php

namespace Smolblog\Core\ContentV1;

use Smolblog\Framework\Objects\ArraySerializable;

/**
 * A piece of data attached to every piece of content.
 *
 * Contrast with a Content Type which only defines data for its particular type. A Content Extension is a smaller
 * piece of data that is applicable to multiple content types, ideally every type.
 *
 * See: WordPress Post Meta
 */
interface ContentExtension extends ArraySerializable {
}
