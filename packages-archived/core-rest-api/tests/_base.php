<?php

namespace Smolblog\CoreRestApi;

use Smolblog\Test\AppTest;

abstract class DataTestBase extends AppTest {
	const INCLUDED_MODELS = [
		\Smolblog\Core\Model::class,
		\Smolblog\CoreRestApi\Model::class,
	];

	protected function createMockServices(): array {
		return [
			...parent::createMockServices(),
		];
	}
}
