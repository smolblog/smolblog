<?php

namespace Smolblog\Core\Post;

enum PostStatus {
	case Draft;
	case Scheduled;
	case Personal;
	case Published;
}
