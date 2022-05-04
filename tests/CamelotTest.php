<?php

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Camelot;

final class CamelotTest extends TestCase
{
	public function testItIsOnlyAModel(): void
	{
		$castle = new Camelot();
		$this->assertEquals(
				"It's only a model.",
				$castle->go()
		);
	}
}
