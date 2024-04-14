<?php

namespace Smolblog\Core\Site;

use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Update settings for a site.
 */
class UpdateSettings extends Command implements AuthorizableMessage {
	/**
	 * Construct the command.
	 *
	 * @throws InvalidCommandParametersException No updated attributes provided.
	 *
	 * @param Identifier  $siteId      ID of site being changed.
	 * @param Identifier  $userId      ID of user making the change.
	 * @param string|null $siteName    Title of site. Null for no change.
	 * @param string|null $siteTagline Tagline/subtitle of site. Null for no change.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly ?string $siteName = null,
		public readonly ?string $siteTagline = null,
	) {
		if (!isset($siteName) && !isset($siteTagline)) {
			throw new InvalidCommandParametersException(command: $this, message: 'No updated attributes provided.');
		}
	}

	/**
	 * User must be an admin to make this change.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserHasPermissionForSite(
			siteId: $this->siteId,
			userId: $this->userId,
			mustBeAdmin: true,
		);
	}
}
