<?php

namespace Smolblog\Core\Post;

use PHPUnit\Framework\TestCase;
use Smolblog\Test\{TestModelHelper, ModelTestToolkit};

final class PostTest extends TestCase {
	use ModelTestToolkit;

	public function setUp(): void {
		$this->model = new Post(new TestModelHelper);
	}
}
