<?php

namespace Smolblog\Core\Site\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Validation\AtLeastOneOf;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Validation\ValidatedKit;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Update settings for a site.
 */
#[AtLeastOneOf('displayName', 'description', 'pictureId')]
readonly class UpdateSiteDetails implements Command, Authenticated, Validated {
	use ValueKit;
	use ValidatedKit;

	/**
	 * Construct the command.
	 *
	 * @throws InvalidValueProperties No updated attributes provided.
	 *
	 * @param UuidInterface      $siteId      ID of site being changed.
	 * @param UuidInterface      $userId      ID of user making the change.
	 * @param string|null        $displayName Title of the site.
	 * @param string|null        $description Description or tagline for the site.
	 * @param UuidInterface|null $pictureId   ID of a picture Media for the site's avatar.
	 */
	public function __construct(
		public UuidInterface $siteId,
		public UuidInterface $userId,
		public ?string $displayName = null,
		public ?string $description = null,
		public ?UuidInterface $pictureId = null,
	) {
		$this->validate();
	}
}
