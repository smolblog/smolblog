<?php

namespace Smolblog\Core\Channel\Services;

use Smolblog\Core\Channel\Commands\CompleteContentPush;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Service\Command\CommandHandlerService;

/**
 * Undocumented class
 */
class DefaultChannelHandlerAsyncRouter implements CommandHandlerService {
	public function routeCommand(CompleteContentPush $command) {
		if (!is_subclass_of($command->service, DefaultChannelHandler::class, allow_string: true)) {
			throw new CodePathNotSupported(
				message: "CompleteContentPush is only for subclasses of " . DefaultChannelHandler::class,
			);
		}
	}
}
