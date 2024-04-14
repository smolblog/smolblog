<?php

namespace Smolblog\Api\Site;

use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Foundation\Value;

/**
 * Setting body.
 */
readonly class SiteSettingsPayload extends Value {
	/**
	 * Create the payload.
	 *
	 * @throws BadRequest No updated attributes provided.
	 *
	 * @param string|null $title   Title for the site. Null for no change.
	 * @param string|null $tagline Tagline/subtitle for the site. Null for no change.
	 */
	public function __construct(
		public readonly ?string $title = null,
		public readonly ?string $tagline = null,
	) {
		if (!isset($title) && !isset($tagline)) {
			throw new BadRequest('No updated attributes provided.');
		}
	}
}
