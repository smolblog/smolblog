<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Test\TestCase;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\Memoizable;

final readonly class TestMemoizableQuery extends Query implements Memoizable {
	public function __construct(public readonly string $key, public readonly string $aux) { parent::__construct(); }
	public function getMemoKey(): string {
		// We are using an intentionally bad function here to prove the short-circuiting of a memoized query.
		return $this->key;
	}
}

final class QueryMemoizationServiceTest extends TestCase {
	public function testAQueryWillNotBeStoppedIfNoMemoExists() {
		$service = new QueryMemoizationService();
		$query = new TestMemoizableQuery(key: 'one', aux: 'two');

		$service->checkMemo($query);
		$this->assertFalse($query->isPropagationStopped());
		$this->assertNull($query->results());
	}

	public function testAQueryWillBeStoppedAndGivenTheMemoizedValueIfPresent() {
		$expected = '3e98f217-2190-4300-ad8a-ded67dfac103';
		$service = new QueryMemoizationService();

		$queryOne = new TestMemoizableQuery(key: 'one', aux: 'two');
		$queryOne->setResults($expected);
		$service->setMemo($queryOne);

		$queryTwo = new TestMemoizableQuery(key: 'one', aux: 'four');
		$service->checkMemo($queryTwo);

		$this->assertTrue($queryTwo->isPropagationStopped());
		$this->assertEquals($expected, $queryTwo->results());
	}
}
