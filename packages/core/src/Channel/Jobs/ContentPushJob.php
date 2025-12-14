<?php

namespace Smolblog\Core\Channel\Jobs;

use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Jobs\Job;

/**
 * Asynchronous job to push content to a channel.
 */
readonly class ContentPushJob extends Job {
	/**
	 * Construct the job.
	 *
	 * @param string      $service   AsyncChannelHandler subclass that should handle this command.
	 * @param Content     $content   Content to push.
	 * @param Channel     $channel   Channel to push to.
	 * @param Identifier  $userId    ID of the user who initiatied the push.
	 * @param Identifier  $processId ID of the push process.
	 * @param string|null $method    Optional method to call; default 'completeContentPush'.
	 */
	public function __construct(
		string $service,
		public Content $content,
		public Channel $channel,
		public Identifier $userId,
		public Identifier $processId,
		?string $method = null,
	) {
		parent::__construct(service: $service, method: $method ?? 'completeContentPush');
	}
}
