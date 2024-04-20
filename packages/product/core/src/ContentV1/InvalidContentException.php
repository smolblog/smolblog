<?php

namespace Smolblog\Core\ContentV1;

use InvalidArgumentException;
use Smolblog\Framework\Exceptions\SmolblogException;

/**
 * Thrown when a content object is created with invalid arguments.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class InvalidContentException extends InvalidArgumentException implements SmolblogException {
}
