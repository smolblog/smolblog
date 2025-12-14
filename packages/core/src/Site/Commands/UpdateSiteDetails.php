<?php

namespace Smolblog\Core\Site\Commands;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Update settings for a site.
 */
readonly class UpdateSiteDetails extends Command {
	/**
	 * Construct the command.
	 *
	 * @throws InvalidValueProperties No updated attributes provided.
	 *
	 * @param Identifier      $siteId      ID of site being changed.
	 * @param Identifier      $userId      ID of user making the change.
	 * @param string|null     $displayName Title of the site.
	 * @param string|null     $description Description or tagline for the site.
	 * @param Identifier|null $pictureId   ID of a picture Media for the site's avatar.
	 */
	public function __construct(
		public Identifier $siteId,
		public Identifier $userId,
		public ?string $displayName = null,
		public ?string $description = null,
		public ?Identifier $pictureId = null,
	) {
		if (!isset($displayName) && !isset($description) && !isset($pictureId)) {
			throw new InvalidValueProperties(message: 'No updated attributes provided.');
		}

		parent::__construct();
	}
}
