<?php

namespace Smolblog\Core\Channel\Services;

use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Entities\ChannelHandlerConfiguration;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Service\Registry\ConfiguredRegisterable;
use Smolblog\Foundation\Value\Fields\Identifier;

interface ChannelHandler extends ConfiguredRegisterable {
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
	 * @param Content    $content Content object to push.
	 * @param Channel    $channel Channel to push content to.
	 * @param Identifier $userId  User initiating the push.
	 * @return void
	 */
	public function pushContentToChannel(
		Content $content,
		Channel $channel,
		Identifier $userId
	): void;
}
