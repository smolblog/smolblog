<?php

namespace Smolblog\Core\Site\Entities;

enum SitePermissionLevel: string {
	case None = 'none';
	case Author = 'author';
	case Admin = 'admin';
}
