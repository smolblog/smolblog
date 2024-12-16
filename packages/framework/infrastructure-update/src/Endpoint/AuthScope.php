<?php

namespace Smolblog\Infrastructure\Endpoint;

/**
 * Different authentication scopes.
 *
 * 'Admin' gives access to everything.
 *
 * @see https://indieweb.org/scope
 */
enum AuthScope: string {
	case Profile = 'profile';
	case Email = 'email';

	case Create = 'create';
	case Update = 'update';
	case Delete = 'delete';
	case Admin = 'admin';
}
