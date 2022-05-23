<?php

namespace Smolblog\Core\Definitions;

enum PostStatus {
	case Draft;
	case Scheduled;
	case Personal;
	case Published;
}
