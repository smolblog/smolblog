<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Create a Reblog.
 */
class CreateReblog extends Command implements AuthorizableMessage {
	/**
	 * Generated ID for the reblog.
	 *
	 * @var Identifier
	 */
	public Identifier $reblogId;

	/**
	 * Construct the command
	 *
	 * @param string      $url     URL being reblogged.
	 * @param Identifier  $userId  User creating the reblog.
	 * @param Identifier  $siteId  Site reblog belongs to.
	 * @param boolean     $publish True if published immediately, false if draft.
	 * @param string|null $comment Optional Markdown-formatted comment.
	 */
	public function __construct(
		public readonly string $url,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly bool $publish,
		public readonly ?string $comment = null,
	) {
	}

	/**
	 * Get the authorization query for this command.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserHasPermissionForSite(siteId: $this->siteId, userId: $this->userId, mustBeAuthor: true);
	}
}
