<?php

namespace Smolblog\Core\Channel\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\ExpectedResponse;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;

/**
 * Push the given content to the given channel.
 */
#[ExpectedResponse(type: ContentChannelEntry::class, optional: true)]
readonly class PushContentToChannel implements Command, Authenticated {
	use ValueKit;

	/**
	 * Create the command.
	 *
	 * @param UuidInterface $contentId Content to push.
	 * @param UuidInterface $userId    User making the push.
	 * @param UuidInterface $channelId Channel to push content to.
	 */
	public function __construct(
		public UuidInterface $contentId,
		public UuidInterface $userId,
		public UuidInterface $channelId,
	) {}
}
