<?php

namespace Smolblog\Core\Site\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\ExpectedResponse;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Create a new site with the given user as primary user.
 */
#[ExpectedResponse(type: UuidInterface::class, name: 'id', description: 'ID of the created site')]
readonly class CreateSite implements Command, Authenticated {
	use ValueKit;

	/**
	 * Create the command.
	 *
	 * @param UuidInterface      $userId      User creating the site.
	 * @param string             $key         Uniquely identifiable name for the site.
	 * @param string             $displayName Displayed title for the site.
	 * @param string|null        $description Description or tagline for the site.
	 * @param UuidInterface|null $siteId      Optional ID for the new site.
	 * @param UuidInterface|null $siteUser    User who will manage the site.
	 */
	public function __construct(
		public UuidInterface $userId,
		public string $key,
		public string $displayName,
		public ?string $description = null,
		public ?UuidInterface $siteId = null,
		public ?UuidInterface $siteUser = null,
	) {}
}
