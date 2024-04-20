<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use Smolblog\Core\ContentV1\EditContentCommandKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Objects\Identifier;

/**
 * Set the channels content should syndicate to.
 *
 * @deprecated Migrate to Smolblog\Core\Content
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
