<?php

namespace Smolblog\Core\Channel\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Channel\Commands\PushContentToChannel;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Events\ContentPushStarted;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;
use Smolblog\Foundation\Value\Fields\DateIdentifier;

/**
 * Handle pushing content to channels.
 */
class ContentPushService implements CommandHandlerService {
	/**
	 * Construct the service.
	 *
	 * @param EventDispatcherInterface $eventBus    Dispatch events.
	 * @param SitePermissionsService   $perms       Check permissions.
	 * @param ContentRepo              $contentRepo Get content objects.
	 * @param ChannelRepo              $channelRepo Get channel objects.
	 * @param ChannelHandlerRegistry   $handlers    Get handlers.
	 */
	public function __construct(
		private EventDispatcherInterface $eventBus,
		private SitePermissionsService $perms,
		private ContentRepo $contentRepo,
		private ChannelRepo $channelRepo,
		private ChannelHandlerRegistry $handlers,
	) {
	}

	/**
	 * Handle the PushContentToChannel command
	 *
	 * @param PushContentToChannel $command Command to handle.
	 * @return void
	 */
	#[CommandHandler]
	public function onPushContentToChannel(PushContentToChannel $command) {
		$content = $this->contentRepo->contentById(contentId: $command->contentId);
		if (!isset($content)) {
			throw new EntityNotFound(entityId: $command->contentId, entityName: Content::class);
		}

		$channel = $this->channelRepo->channelById($command->channelId);
		if (!isset($channel)) {
			throw new EntityNotFound(entityId: $command->channelId, entityName: Channel::class);
		}

		if (!$this->perms->canPushContent(userId: $command->userId, siteId: $content->siteId)) {
			throw new CommandNotAuthorized($command);
		}

		$this->handlers->get($channel->handler)->pushContentToChannel(
			content: $content,
			channel: $channel,
			userId: $command->userId
		);
	}
}
