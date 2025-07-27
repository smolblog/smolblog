<?php

namespace Smolblog\Core\Channel\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Channel\Events\ContentPushFailed;
use Smolblog\Core\Channel\Events\ContentPushStarted;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Channel\Jobs\ContentPushJob;
use Smolblog\Foundation\Service\Event\EventListenerService;
use Smolblog\Foundation\Service\Event\ProjectionListener;
use Smolblog\Foundation\Service\Job\JobManager;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;

/**
 * Provides a set of good defaults for projection channel handlers.
 *
 * A Projection-style channel handler can be regenerated without penalty or side-effects. Examples include generating a
 * website or maintaining a feed. Contrast with a channel that updates a web service or sends notifications.
 *
 * Implementing classes should implement the project method as documented: returning a ContentChannelEntry on success
 * and throwing a ContentPushFailure on failure. This class will handle dispatching the required events.
 */
abstract class ProjectionChannelHandler implements ChannelHandler, EventListenerService {
	/**
	 * Event class to use for the initial push event.
	 *
	 * The generic ContentPushedToChannel event is fine, but some channels may want to override with a channel-specific
	 * event.
	 */
	protected const PUSH_EVENT = ContentPushedToChannel::class;

	/**
	 * Construct the service.
	 *
	 * @param EventDispatcherInterface $eventBus For dispatching the events.
	 * @param ChannelRepo              $channels For fetching channels.
	 */
	public function __construct(
		private EventDispatcherInterface $eventBus,
		private ChannelRepo $channels,
	) {
	}

	/**
	 * Dispatch the push event.
	 *
	 * @param Content    $content Content object to push.
	 * @param Channel    $channel Channel to push object to.
	 * @param Identifier $userId  ID of the user who initiated the push.
	 * @return void
	 */
	public function pushContentToChannel(
		Content $content,
		Channel $channel,
		Identifier $userId
	): void {
		$processId = new RandomIdentifier();
		$startEvent = new (static::PUSH_EVENT)(
			content: $content,
			channelId: $channel->getId(),
			userId: $userId,
			aggregateId: $content->siteId,
			processId: $processId,
		);
		$this->eventBus->dispatch($startEvent);
	}

	/**
	 * Handle the push event
	 *
	 * @param ContentPushedToChannel $event          Content push event to handle.
	 * @param Identifier|null        $regenerationId Optional ID for the current regeneration process.
	 * @return void
	 */
	#[ProjectionListener]
	public function handlePushEvent(ContentPushedToChannel $event, ?Identifier $regenerationId = null): void {
		$channel = $this->channels->channelById($event->channelId);
		if (!isset($channel) || $channel?->handler != static::getConfiguration()->key) {
			// Not necessarily an error; we're handling a generic event. But either way, we're done here.
			return;
		}

		$processId = $regenerationId ?? $event->processId;
		$processId ??= new RandomIdentifier();

		try {
			$result = $this->project(
				content: $event->content,
				channel: $channel,
				userId: $event->userId,
				processId: $processId,
			);
		} catch (ContentPushException $exc) {
			$this->eventBus->dispatch(new ContentPushFailed(
				contentId: $event->content->id,
				channelId: $channel->getId(),
				processId: $processId,
				message: $exc->getMessage(),
				userId: $event->userId,
				aggregateId: $event->content->siteId,
				details: $exc->details,
			));
			return;
		}

		$this->eventBus->dispatch(new ContentPushSucceeded(
			contentId: $event->content->id,
			channelId: $channel->getId(),
			processId: $processId,
			url: $result->url,
			userId: $event->userId,
			aggregateId: $event->content->siteId,
			details: $result->details,
		));
	}

	/**
	 * Push the given content to the given channel.
	 *
	 * @throws ContentPushFailure On failure.
	 *
	 * @param Content    $content   Content object to push.
	 * @param Channel    $channel   Channel to push object to.
	 * @param Identifier $userId    ID of the user who initiated the push.
	 * @param Identifier $processId ID of this particular push or regeneration process.
	 * @return ContentChannelEntry Information about the successfully completed push.
	 */
	abstract protected function project(
		Content $content,
		Channel $channel,
		Identifier $userId,
		?Identifier $processId
	): ContentChannelEntry;
}
