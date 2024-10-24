<?php

namespace Smolblog\Core\Channel\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushFailed;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Channel\Jobs\ContentPushJob;
use Smolblog\Foundation\Service\Job\JobManager;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Provides a set of good defaults for most channel handlers.
 *
 * Implementing classes should implement the push method as documented: returning a ContentChannelEntry on success and
 * throwing a ContentPushFailure on failure. This class will handle dispatching the required events.
 */
abstract class DefaultChannelHandler implements ChannelHandler {
	/**
	 * Construct the service.
	 *
	 * @param JobManager               $jobManager For launching the asynchronous command.
	 * @param EventDispatcherInterface $eventBus   For dispatching the events.
	 */
	public function __construct(
		private JobManager $jobManager,
		private EventDispatcherInterface $eventBus,
	) {
	}

	/**
	 * Dispatch an async process to complete the push later.
	 *
	 * @param Content    $content      Content object to push.
	 * @param Channel    $channel      Channel to push object to.
	 * @param Identifier $userId       ID of the user who initiated the push.
	 * @param Identifier $startEventId ID of the event indicating the start of this push.
	 * @return void
	 */
	public function pushContentToChannel(
		Content $content,
		Channel $channel,
		Identifier $userId,
		Identifier $startEventId
	): void {
		$this->jobManager->enqueue(
			new ContentPushJob(
				content: $content,
				channel: $channel,
				userId: $userId,
				startEventId: $startEventId,
				service: static::class,
			)
		);
	}

	/**
	 * Handle the ContentPushJob command when it is eventually executed.
	 *
	 * @param Content    $content      Content object to push.
	 * @param Channel    $channel      Channel to push object to.
	 * @param Identifier $userId       ID of the user who initiated the push.
	 * @param Identifier $startEventId ID of the event indicating the start of this push.
	 * @return void
	 */
	public function completeContentPush(
		Content $content,
		Channel $channel,
		Identifier $userId,
		Identifier $startEventId
	): void {
		try {
			$result = $this->push(
				content: $content,
				channel: $channel,
				userId: $userId,
				startEventId: $startEventId,
			);
		} catch (ContentPushException $exc) {
			$this->eventBus->dispatch(new ContentPushFailed(
				contentId: $content->id,
				channelId: $channel->getId(),
				startEventId: $startEventId,
				message: $exc->getMessage(),
				userId: $userId,
				aggregateId: $content->siteId,
				details: $exc->details,
			));
			return;
		}

		$this->eventBus->dispatch(new ContentPushSucceeded(
			contentId: $content->id,
			channelId: $channel->getId(),
			startEventId: $startEventId,
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
	 * @param Content    $content      Content object to push.
	 * @param Channel    $channel      Channel to push object to.
	 * @param Identifier $userId       ID of the user who initiated the push.
	 * @param Identifier $startEventId ID of the event indicating the start of this push.
	 * @return ContentChannelEntry Information about the successfully completed push.
	 */
	abstract protected function push(
		Content $content,
		Channel $channel,
		Identifier $userId,
		Identifier $startEventId
	): ContentChannelEntry;
}