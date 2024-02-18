<?php

namespace Smolblog\Core\Site;

use InvalidArgumentException;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\RandomIdentifier;

/**
 * Create a new site with the given user as primary user.
 */
class CreateSite extends Command implements AuthorizableMessage {
	/**
	 * Create the command.
	 *
	 * @throws InvalidArgumentException When $baseUrl is not a URL.
	 *
	 * @param Identifier      $userId      User who will manage the site.
	 * @param string          $handle      Uniquely identifiable name for the site.
	 * @param string          $displayName Displayed title for the site.
	 * @param string          $baseUrl     Base URL for the site.
	 * @param Identifier      $siteId      Optional ID for the new site.
	 * @param Identifier|null $commandUser If the user creating the site is not the manager.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly string $handle,
		public readonly string $displayName,
		public readonly string $baseUrl,
		public readonly Identifier $siteId = new RandomIdentifier(),
		public readonly ?Identifier $commandUser = null
	) {
		if (filter_var($this->baseUrl, FILTER_VALIDATE_URL) === false) {
			throw new InvalidArgumentException($this->baseUrl . ' is not a valid URL.');
		}
	}

	/**
	 * Determine if the given user can create a site.
	 *
	 * Uses $this->userId unless $this->commandUser is defined.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserCanCreateSites($this->commandUser ?? $this->userId);
	}
}
