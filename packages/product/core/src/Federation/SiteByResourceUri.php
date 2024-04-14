<?php

namespace Smolblog\Core\Federation;

use Smolblog\Foundation\Value\Messages\Query;

/**
 * Translate a resource URI into a Site.
 *
 * A resource URI could be an `acct:` URI or a standard `http:` URI. If a site with id 12345 lives on the smol.blog
 * server with the 'alice' handle and the 'bob.com' domain, then all of these URIs should resolve to the same site:
 * - acct:alice@smol.blog
 * - acct:alice@bob.com
 * - acct:alice.smol.blog
 * - acct:bob.com
 * - https://alice.smol.blog
 * - https://bob.com
 */
readonly class SiteByResourceUri extends Query {
	/**
	 * Create the query.
	 *
	 * @param string $resource Resource string to search by.
	 */
	public function __construct(
		public readonly string $resource,
	) {
	}
}
