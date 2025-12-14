<?php

namespace Smolblog\Core\Site\Commands;

use Smolblog\Foundation\Service\Command\ExpectedResponse;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Create a new site with the given user as primary user.
 */
#[ExpectedResponse(type: Identifier::class, name: 'id', description: 'ID of the created site')]
readonly class CreateSite extends Command {
	/**
	 * Create the command.
	 *
	 * @param Identifier      $userId      User creating the site.
	 * @param string          $key         Uniquely identifiable name for the site.
	 * @param string          $displayName Displayed title for the site.
	 * @param string|null     $description Description or tagline for the site.
	 * @param Identifier|null $siteId      Optional ID for the new site.
	 * @param Identifier|null $siteUser    User who will manage the site.
	 */
	public function __construct(
		public Identifier $userId,
		public string $key,
		public string $displayName,
		public ?string $description = null,
		public ?Identifier $siteId = null,
		public ?Identifier $siteUser = null
	) {
		parent::__construct();
	}
}
