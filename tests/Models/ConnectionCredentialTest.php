<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Test\{TestModelHelper, ModelTestToolkit};

final class ConnectionCredentialTest extends TestCase {
	use ModelTestToolkit;

	private function createModel() {
		return new ConnectionCredential(new TestModelHelper);
	}
}
