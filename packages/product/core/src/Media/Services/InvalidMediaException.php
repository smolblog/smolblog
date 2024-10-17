<?php

namespace Smolblog\Core\Media;

use Psr\Http\Message\UploadedFileInterface;
use Throwable;

/**
 * Indicates that a paricular piece of media could not be handled.
 */
interface InvalidMediaException extends Throwable {
}
