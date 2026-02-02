<?php

namespace Smolblog\Core\Channel\Jobs;

use Cavatappi\Foundation\Job\Job;
use Cavatappi\Foundation\Job\JobKit;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Content\Entities\Content;

/**
 * Asynchronous job to push content to a channel.
 */
readonly class ContentPushJob implements Job {
	use JobKit;

	/**
	 * Construct the job.
	 *
	 * @param string        $service   AsyncChannelHandler subclass that should handle this command.
	 * @param Content       $content   Content to push.
	 * @param Channel       $channel   Channel to push to.
	 * @param UuidInterface $userId    ID of the user who initiatied the push.
	 * @param UuidInterface $processId ID of the push process.
	 * @param string|null   $method    Optional method to call; default 'completeContentPush'.
	 */
	public function __construct(
		string $service,
		public Content $content,
		public Channel $channel,
		public UuidInterface $userId,
		public UuidInterface $processId,
		?string $method = null,
	) {
		$this->service = $service;
		$this->method = $method ?? 'completeContentPush';
	}
}
