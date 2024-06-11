<?php

namespace Smolblog\Foundation\Value\Messages;
use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Test\TestCase;

#[CoversClass(Query::class)]
final class QueryTest extends TestCase {
	public function testItCanStoreAndRetrieveAResult() {
		$query = new class('say') extends Query {
			public function __construct(public string $name) {}
		};

		$query->setResults('hello');

		$this->assertEquals('hello', $query->results());
	}
}
