<?php

namespace Smolblog\Framework\Infrastructure;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\MemoizableQueryKit;
use Smolblog\Framework\Messages\Query;

final class TestMemoizableQuery extends Query implements MemoizableQuery {
	use MemoizableQueryKit;
	public function __construct(public readonly string $key, public readonly string $aux) {}
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
		$this->assertNull($query->results);
	}

	public function testAQueryWillBeStoppedAndGivenTheMemoizedValueIfPresent() {
		$expected = '3e98f217-2190-4300-ad8a-ded67dfac103';
		$service = new QueryMemoizationService();

		$queryOne = new TestMemoizableQuery(key: 'one', aux: 'two');
		$queryOne->results = $expected;
		$service->setMemo($queryOne);

		$queryTwo = new TestMemoizableQuery(key: 'one', aux: 'four');
		$service->checkMemo($queryTwo);

		$this->assertTrue($queryTwo->isPropagationStopped());
		$this->assertEquals($expected, $queryTwo->results);
	}
}