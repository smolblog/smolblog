<?php

namespace Smolblog\Framework\Exceptions;

use Throwable;

/**
 * Common interface for all Smolblog-originated Exceptions to "implement." This allows a single statement to
 * catch all Exceptions from this library.
 */
interface SmolblogException extends Throwable {
}
