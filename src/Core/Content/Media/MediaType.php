<?php

namespace Smolblog\Core\Content\Media;

/**
 * Broad 'types' for media uploads.
 */
enum MediaType: string {
	case Image = 'image';
	case Video = 'video';
	case Audio = 'audio';
	case File = 'file';
}
