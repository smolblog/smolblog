<?php

namespace Smolblog\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\Constraints\DomainEventChecker;

class AppTest extends TestCase {
	const INCLUDED_MODELS = [];

	protected TestApp $app;

	protected function setUp(): void {
		$this->app = new TestApp(models: static::INCLUDED_MODELS, services: $this->createMockServices());
	}

	protected function createMockServices(): array {
		return [];
	}
}
