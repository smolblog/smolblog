<?php

namespace Smolblog\ContentProvenance;

use Exception;
use Smolblog\Framework\Exceptions\SmolblogException;

/**
 * Indicates a Manifest was created with invalid values.
 */
class InvalidManifestException extends Exception implements SmolblogException {
}
