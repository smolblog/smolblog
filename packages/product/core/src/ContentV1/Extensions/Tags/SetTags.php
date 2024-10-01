<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Set the tags for a piece of content.
 */
class SetTags extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Create the command.
	 *
	 * @param Identifier $siteId    Site the content is on.
	 * @param Identifier $userId    User making the change.
	 * @param Identifier $contentId Content being modified.
	 * @param array      $tags      Text of the tags to set.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $contentId,
		public readonly array $tags,
	) {
	}
}
