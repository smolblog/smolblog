<?php

namespace Smolblog\Core\Media\Entities;

use Cavatappi\Foundation\Value;

/**
 * A piece of data attached to every piece of media.
 *
 * Useful for attaching elements directly to media files, such as licensing and attribution information.
 *
 * See: WordPress Post Meta
 */
interface MediaExtension extends Value {}
