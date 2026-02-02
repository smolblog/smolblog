<?php

namespace Smolblog\Core\Channel\Services;

use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Job\JobManager;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Channel\Events\ContentPushFailed;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Channel\Jobs\ContentPushJob;

/**
 * Provides a set of good defaults for async channel handlers.
 *
 * Implementing classes should implement the push method as documented: returning a ContentChannelEntry on success and
 * throwing a ContentPushFailure on failure. This class will handle dispatching the required events.
 */
abstract class AsyncChannelHandler implements ChannelHandler {
	/**
	 * Construct the service.
	 *
	 * @param JobManager               $jobManager For launching the asynchronous command.
	 * @param EventDispatcherInterface $eventBus   For dispatching the events.
	 */
	public function __construct(
		private JobManager $jobManager,
		private EventDispatcherInterface $eventBus,
	) {}

	/**
	 * Dispatch an async process to complete the push later.
	 *
	 * @param Content       $content Content object to push.
	 * @param Channel       $channel Channel to push object to.
	 * @param UuidInterface $userId  ID of the user who initiated the push.
	 * @return void
	 */
	public function pushContentToChannel(
		Content $content,
		Channel $channel,
		UuidInterface $userId,
	): void {
		$processId = UuidFactory::random();
		$startEvent = new ContentPushedToChannel(
			content: $content,
			channelId: $channel->id,
			userId: $userId,
			aggregateId: $content->siteId,
			processId: $processId,
		);
		$this->eventBus->dispatch($startEvent);

		$this->jobManager->enqueue(
			new ContentPushJob(
				content: $content,
				channel: $channel,
				userId: $userId,
				processId: $processId,
				service: static::class,
			),
		);
	}

	/**
	 * Handle the ContentPushJob command when it is eventually executed.
	 *
	 * @param Content       $content   Content object to push.
	 * @param Channel       $channel   Channel to push object to.
	 * @param UuidInterface $userId    ID of the user who initiated the push.
	 * @param UuidInterface $processId ID of this particular push process.
	 * @return void
	 */
	public function completeContentPush(
		Content $content,
		Channel $channel,
		UuidInterface $userId,
		UuidInterface $processId,
	): void {
		try {
			$result = $this->push(
				content: $content,
				channel: $channel,
				userId: $userId,
				processId: $processId,
			);
		} catch (ContentPushException $exc) {
			$this->eventBus->dispatch(new ContentPushFailed(
				contentId: $content->id,
				channelId: $channel->id,
				processId: $processId,
				message: $exc->getMessage(),
				userId: $userId,
				aggregateId: $content->siteId,
				details: $exc->details,
			));
			return;
		}

		$this->eventBus->dispatch(new ContentPushSucceeded(
			contentId: $content->id,
			channelId: $channel->id,
			processId: $processId,
			userId: $userId,
			aggregateId: $content->siteId,
			url: $result->url,
			details: $result->details,
		));
	}

	/**
	 * Push the given content to the given channel.
	 *
	 * @throws ContentPushFailure On failure.
	 *
	 * @param Content       $content   Content object to push.
	 * @param Channel       $channel   Channel to push object to.
	 * @param UuidInterface $userId    ID of the user who initiated the push.
	 * @param UuidInterface $processId ID of this particular push process.
	 * @return ContentChannelEntry Information about the successfully completed push.
	 */
	abstract protected function push(
		Content $content,
		Channel $channel,
		UuidInterface $userId,
		UuidInterface $processId,
	): ContentChannelEntry;
}
