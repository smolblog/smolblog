<?php

namespace Smolblog\ActivityPub\Handles;

use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\Entity;
use Smolblog\Framework\Objects\Identifier;

/**
 * Represents an ActivityPub handle for a site.
 */
class ActivityPubHandle extends Entity {
	/**
	 * Create the link
	 *
	 * @param string          $handle ActivityPub handle.
	 * @param Identifier      $siteId ID of the site this handle belongs to.
	 * @param Identifier|null $id     ID for this link.
	 */
	public function __construct(
		public readonly string $handle,
		public readonly Identifier $siteId,
		Identifier $id = new DateIdentifier(),
	) {
		parent::__construct(id: $id);
	}
}
