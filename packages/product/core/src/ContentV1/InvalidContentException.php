<?php

namespace Smolblog\Core\ContentV1;

use InvalidArgumentException;
use Smolblog\Framework\Exceptions\SmolblogException;

/**
 * Thrown when a content object is created with invalid arguments.
 */
class InvalidContentException extends InvalidArgumentException implements SmolblogException {
}
