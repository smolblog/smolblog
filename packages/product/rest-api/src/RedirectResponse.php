<?php

namespace Smolblog\Api;

use Smolblog\Foundation\Value;

/**
 * Response indicating a redirect (301 or 302) should be given.
 */
readonly class RedirectResponse extends Value {
	/**
	 * Construct the response
	 *
	 * @param string  $url       URL to redirect to.
	 * @param boolean $permanent True if all requests from all users to this endpoint should be redirected to the URL.
	 *   Default false.
	 */
	public function __construct(
		public readonly string $url,
		public readonly bool $permanent = false,
	) {
	}
}
