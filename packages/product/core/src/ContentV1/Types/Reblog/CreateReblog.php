<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\Identifier;

/**
 * Create a Reblog.
 */
class CreateReblog extends Command implements AuthorizableMessage {
	/**
	 * Construct the command
	 *
	 * @param string      $url       URL being reblogged.
	 * @param Identifier  $userId    User creating the reblog.
	 * @param Identifier  $siteId    Site reblog belongs to.
	 * @param boolean     $publish   True if published immediately, false if draft.
	 * @param string|null $comment   Optional Markdown-formatted comment.
	 * @param Identifier  $contentId ID for the new note; will auto-generate if not given.
	 */
	public function __construct(
		public readonly string $url,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly bool $publish = false,
		public readonly ?string $comment = null,
		public readonly Identifier $contentId = new DateIdentifier(),
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
