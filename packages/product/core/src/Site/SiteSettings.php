<?php

namespace Smolblog\Core\Site;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Settings for a site.
 */
readonly class SiteSettings extends Value implements Entity {
	use EntityKit;
	/**
	 * Create the payload.
	 *
	 * @param Identifier  $siteId  ID for the site.
	 * @param string|null $title   Title for the site.
	 * @param string|null $tagline Tagline/subtitle for the site.
	 */
	public function __construct(
		Identifier $siteId,
		public readonly string $title,
		public readonly string $tagline,
	) {
		$this->id = $siteId;
	}
}
