<?php

namespace Smolblog\Core\Site;

use Smolblog\Framework\Objects\Entity;
use Smolblog\Framework\Objects\Identifier;

/**
 * Settings for a site.
 */
class SiteSettings extends Entity {
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
		parent::__construct(id: $siteId);
	}
}
