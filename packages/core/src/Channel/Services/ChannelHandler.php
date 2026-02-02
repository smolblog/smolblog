<?php

namespace Smolblog\Core\Channel\Services;

use Cavatappi\Foundation\Registry\ConfiguredRegisterable;
use Cavatappi\Foundation\Service;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Entities\ChannelHandlerConfiguration;
use Smolblog\Core\Content\Entities\Content;

interface ChannelHandler extends ConfiguredRegisterable, Service {
	/**
	 * Get the configuration for this handler.
	 *
	 * @return ChannelHandlerConfiguration
	 */
	public static function getConfiguration(): ChannelHandlerConfiguration;

	/**
	 * Push the content to the channel and dispatch all relevant events.
	 *
	 * The implementing class should perform its actions and dispatch a ContentPushedToChannel event (or a subclass)
	 * upon success. Those using asychronous processes are encouraged to use the ContentPush* events.
	 *
	 * @param Content       $content Content object to push.
	 * @param Channel       $channel Channel to push content to.
	 * @param UuidInterface $userId  User initiating the push.
	 * @return void
	 */
	public function pushContentToChannel(
		Content $content,
		Channel $channel,
		UuidInterface $userId,
	): void;
}
