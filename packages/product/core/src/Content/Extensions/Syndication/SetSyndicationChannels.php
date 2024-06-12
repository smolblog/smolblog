<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use Smolblog\Core\Content\EditContentCommandKit;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Set the channels content should syndicate to.
 */
class SetSyndicationChannels extends Command implements AuthorizableMessage {
	use EditContentCommandKit;

	/**
	 * Construct the command.
	 *
	 * @param Identifier   $userId    User making the change.
	 * @param Identifier   $siteId    Site with the content.
	 * @param Identifier   $contentId Content being modified.
	 * @param Identifier[] $channels  Channels to syndicate the content to upon posting.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly Identifier $contentId,
		public readonly array $channels
	) {
	}
}
