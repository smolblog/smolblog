<?php

namespace Smolblog\Core\Definitions;

/*
 * Levels of security:
 *
 * - Anonymous: General internet-level requests
 * - Registered: Request is attached to a logged-in user
 * - Admin: Can post to the blog
 * - Owner: Can manage Admins and change sensitive blog settings
 * - Root: Site-wide administrators
 */

enum SecurityLevel: int {
	case Anonymous = 0;
	case Registered = 1;
	case Admin = 5;
	case Owner = 10;
	case Root = 100;
}
