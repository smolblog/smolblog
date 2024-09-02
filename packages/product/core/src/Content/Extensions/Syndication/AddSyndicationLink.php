<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Manually add a syndication link to a piece of content.
 *
 * This is for a situation where a user manually adds a URL to a content's links.
 */
class AddSyndicationLink extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier $userId    User making the change.
	 * @param Identifier $siteId    Site with the content.
	 * @param Identifier $contentId Content being modified.
	 * @param string     $url       URL to the syndicated content.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly Identifier $contentId,
		public readonly string $url
	) {
	}
}
