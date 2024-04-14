<?php

namespace Smolblog\Api\Site;

use Smolblog\Foundation\Value;

/**
 * Expected payload for creating a new site.
 */
readonly class NewSitePayload extends Value {
	/**
	 * Create the payload.
	 *
	 * @param string $handle      Short unique name for the site.
	 * @param string $displayName Displayed name for the site.
	 */
	public function __construct(
		public readonly string $handle,
		public readonly string $displayName
	) {
	}
}
