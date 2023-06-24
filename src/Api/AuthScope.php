<?php

namespace Smolblog\Api;

/**
 * Different authentication scopes.
 *
 * Use 'Identified' to indicate that a user only needs to be authenticated. Other scopes must be given to apps.
 *
 * 'Admin' gives access to everything.
 *
 * @see https://indieweb.org/scope
 */
enum AuthScope: string {
	case Identified = 'id';
	case Profile = 'profile';
	case Email = 'email';

	case Read = 'read';
	case Follow = 'follow';
	case Mute = 'mute';
	case Block = 'block';
	case Channels = 'channels';

	case Create = 'create';
	case Draft = 'draft';
	case Update = 'update';
	case Delete = 'delete';
	case Media = 'media';

	case Admin = 'admin';
}
