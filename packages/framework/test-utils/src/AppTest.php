<?php

namespace Smolblog\Test;

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
