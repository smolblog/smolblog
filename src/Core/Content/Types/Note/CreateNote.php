<?php

namespace Smolblog\Core\Content\Types\Note;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Create a Note.
 */
class CreateNote extends Command implements AuthorizableMessage {
	/**
	 * Generated ID for the note.
	 *
	 * @var Identifier
	 */
	public Identifier $noteId;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId  Site for this note.
	 * @param Identifier $userId  User authoring this note.
	 * @param string     $text    Markdown-formatted text of the note.
	 * @param boolean    $publish True to publish note immediately.
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
