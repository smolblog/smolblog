<?php

namespace Smolblog\Core\Channel\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Channel\Commands\CompleteContentPush;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushFailed;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Foundation\Service\Command\CommandBus;
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
	 * @param CommandBus               $commandBus For launching the asynchronous command.
	 * @param EventDispatcherInterface $eventBus   For dispatching the events.
	 */
	public function __construct(
		private CommandBus $commandBus,
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
		$this->commandBus->executeAsync(
			new CompleteContentPush(
				content: $content,
				channel: $channel,
				userId: $userId,
				startEventId: $startEventId,
				service: static::class,
			)
		);
	}

	/**
	 * Handle the CompleteContentPush command when it is eventually executed.
	 *
	 * @param CompleteContentPush $command Async command being executed.
	 * @return void
	 */
	public function completeContentPush(CompleteContentPush $command): void {
		try {
			$result = $this->push(
				content: $command->content,
				channel: $command->channel,
				userId: $command->userId,
				startEventId: $command->startEventId,
			);
		} catch (ContentPushException $exc) {
			$this->eventBus->dispatch(new ContentPushFailed(
				contentId: $command->content->id,
				channelId: $command->channel->getId(),
				startEventId: $command->startEventId,
				message: $exc->getMessage(),
				userId: $command->userId,
				aggregateId: $command->content->siteId,
				details: $exc->details,
			));
			return;
		}

		$this->eventBus->dispatch(new ContentPushSucceeded(
			contentId: $command->content->id,
			channelId: $command->channel->getId(),
			startEventId: $command->startEventId,
			userId: $command->userId,
			aggregateId: $command->content->siteId,
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
