<?php

namespace Smolblog\Core\Content\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableSupertypeKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;

/**
 * A piece of data attached to every piece of content.
 *
 * Contrast with a Content Type which only defines data for its particular type. A Content Extension is a smaller
 * piece of data that is applicable to multiple content types, ideally every type.
 *
 * See: WordPress Post Meta
 */
abstract readonly class ContentExtension extends Value implements SerializableValue {
	use SerializableSupertypeKit;
}
