<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Test\{TestModelHelper, ModelTestToolkit};

final class ConnectionCredentialTest extends TestCase {
	use ModelTestToolkit;

	public function setUp(): void {
		$this->model = new ConnectionCredential(new TestModelHelper);
	}
}
