<?php

namespace Smolblog\Foundation\Test\DiscoveryTestFixture\Others;

use Smolblog\Foundation\Test\DiscoveryTestFixture\SameDirectoryService;
use Smolblog\Foundation\Test\DiscoveryTestFixture\SomeFolder\{SomeAbstractServiceInterfaceClass, SomeInterface};

final class NonServiceClass{
	public function __construct(
		private SomeInterface $one,
		private SomeAbstractServiceInterfaceClass $two,
		private SameDirectoryService $three) {
	}
}
