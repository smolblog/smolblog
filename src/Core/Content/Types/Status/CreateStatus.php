<?php

namespace Smolblog\Core\Content\Types\Status;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Create a Status.
 */
class CreateStatus extends Command implements AuthorizableMessage {
	use StoppableMessageKit;

	/**
	 * Generated ID for the status.
	 *
	 * @var Identifier
	 */
	public Identifier $statusId;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId  Site for this status.
	 * @param Identifier $userId  User authoring this status.
	 * @param string     $text    Markdown-formatted text of the status.
	 * @param boolean    $publish True to publish status immediately.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly string $text,
		public readonly bool $publish,
	) {
	}

	/**
	 * User must be able to author posts on this site.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserHasPermissionForSite(
			siteId: $this->siteId,
			userId: $this->userId,
			mustBeAuthor: true,
			mustBeAdmin: false,
		);
	}
}
