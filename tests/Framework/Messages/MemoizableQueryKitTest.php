<?php

namespace Smolblog\Framework\Messages;

use PHPUnit\Framework\TestCase;

final class AQuery {
	use MemoizableQueryKit;
	public function __construct(public readonly string $argA, public readonly string $argB) {}
}

final class BQuery {
	use MemoizableQueryKit;
	public function __construct(public readonly string $argA, public readonly string $argB) {}
}

final class MemoizableQueryKitTest extends TestCase {
	public function testTheSameClassWithTheSameParametersWillHaveTheSameMemoKey() {
		$queryOne = new AQuery(argA: 'One', argB: 'Two');
		$queryTwo = new AQuery(argA: 'One', argB: 'Two');

		$this->assertEquals($queryOne->getMemoKey(), $queryTwo->getMemoKey());
	}

	public function testTheSameClassWithDifferentParametersWillNotHaveTheSameMemoKey() {
		$queryOne = new AQuery(argA: 'One', argB: 'Two');
		$queryTwo = new AQuery(argA: 'Three', argB: 'Four');

		$this->assertNotEquals($queryOne->getMemoKey(), $queryTwo->getMemoKey());
	}

	public function testDifferentClassesWithTheSameParametersWillNotHaveTheSameMemoKey() {
		$queryOne = new AQuery(argA: 'One', argB: 'Two');
		$queryTwo = new BQuery(argA: 'One', argB: 'Two');

		$this->assertNotEquals($queryOne->getMemoKey(), $queryTwo->getMemoKey());
	}
}