<?php

namespace Smolblog\Core\Post;

enum PostStatus: string {
	case Draft = 'draft';
	case Scheduled = 'scheduled';
	case Personal = 'personal';
	case Published = 'published';
}
