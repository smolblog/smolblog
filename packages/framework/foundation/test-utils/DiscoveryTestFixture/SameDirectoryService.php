<?php

namespace Smolblog\Foundation\Test\DiscoveryTestFixture;

use Smolblog\Foundation\Service;
use Smolblog\Foundation\Service\Command\CommandBus;

final class SameDirectoryService implements Service {
	public function __construct(private CommandBus $commandBus)
	{

	}
}
