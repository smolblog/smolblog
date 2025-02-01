<?php

namespace Smolblog\Test;

use Smolblog\Test\BasicApp\App;

class AppTest extends TestCase {
	const INCLUDED_MODELS = [];

	protected App $app;

	protected function setUp(): void {
		$this->app = new App(models: static::INCLUDED_MODELS, services: $this->createMockServices());
	}

	protected function createMockServices(): array {
		return [];
	}
}
