<?php

namespace Smolblog\Core\Test\Stubs;

use Smolblog\Core\Channel\Entities\ChannelHandlerConfiguration;
use Smolblog\Core\Channel\Services\ChannelHandler;

/**
 * Provices a ChannelHandler with key 'testmock'
 */
abstract class ChannelHandlerTestBase implements ChannelHandler {
	public static function getConfiguration(): ChannelHandlerConfiguration {
		return new ChannelHandlerConfiguration(
			key: 'testmock',
			displayName: 'Test',
		);
	}
}
