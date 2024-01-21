<?php

namespace Smolblog\Core\Content\Commands;

use DateTimeInterface;
use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Edit the base attributes of a piece of content.
 */
class EditContentBaseAttributes extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command
	 *
	 * @throws InvalidCommandParametersException Thrown if no updated attributes are given.
	 *
	 * @param Identifier             $contentId        ID of content to edit.
	 * @param Identifier             $userId           ID of user making this change.
	 * @param Identifier             $siteId           ID of site this content exists on.
	 * @param string|null            $permalink        Updated permalink.
	 * @param DateTimeInterface|null $publishTimestamp Updated publish time.
	 * @param Identifier|null        $authorId         Update author ID.
	 */
	public function __construct(
		public readonly Identifier $contentId,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly ?string $permalink = null,
		public readonly ?DateTimeInterface $publishTimestamp = null,
		public readonly ?Identifier $authorId = null,
	) {
		if (!isset($this->permalink) && !isset($this->publishTimestamp) && !isset($this->authorId)) {
			throw new InvalidCommandParametersException(command: $this);
		}
	}
}
