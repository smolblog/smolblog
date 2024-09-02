<?php

namespace Smolblog\Framework\Exceptions;

use InvalidArgumentException;

/**
 * Exception to throw if a message is given invalid attributes.
 *
 * @deprecated Migrate to Smolblog\Foundation classes
 */
class InvalidMessageAttributesException extends InvalidArgumentException implements SmolblogException {
}
