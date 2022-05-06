<?php

namespace Smolblog\Core\Definitions;

/*
 * Levels of security:
 *
 * - Anonymous: General internet-level requests
 * - Registered: Request is attached to a logged-in user
 * - Contributor: Can post to the blog
 * - Moderator: Can modify all posts on the blog
 * - Admin: Can manage other users and change sensitive blog settings
 * - Root: Site-wide administrators
 */

enum SecurityLevel: int {
	case Anonymous = 0;
	case Registered = 1;
	case Contributor = 5;
	case Moderator = 10;
	case Admin = 20;
	case Root = 100;
}
