<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Registry\Registry;

class CommandHandlerRegistry implements Registry {
	public static function getInterfaceToRegister(): string {
		return CommandHandler::class;
	}

	public function configure(array $configuration): void {

	}
}
