<?php

namespace Smolblog\Core\ContentV1\Media;

/**
 * Broad 'types' for media uploads.
 *
 * @deprecated Migrate to Smolblog\Core\Media
 */
enum MediaType: string {
	case Image = 'image';
	case Video = 'video';
	case Audio = 'audio';
	case File = 'file';
}
