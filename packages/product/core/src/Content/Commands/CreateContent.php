<?php

namespace Smolblog\Core\Content\Commands;

use Smolblog\Core\Content;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

class CreateContent extends Command implements AuthorizableMessage {
	public function __construct(
		public Identifier $userId,
		public Content $content,
	) {
	}

	/**
	 * Check if the user has author permissions.
	 *
	 * @return UserHasPermissionForSite
	 */
	public function getAuthorizationQuery(): UserHasPermissionForSite {
		return new UserHasPermissionForSite(
			siteId: $this->content->siteId,
			userId: $this->userId,
			mustBeAuthor: true,
		);
	}
}
