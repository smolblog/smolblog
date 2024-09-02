<?php

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\Memoizable;
use Smolblog\Foundation\Value\Traits\MemoizableKit;
use Smolblog\Test\TestCase;

readonly class ExampleMemoizableQuery extends Query implements Memoizable {
	use MemoizableKit;
	public function __construct(public string $name, public Identifier $id) { parent::__construct(); }
}

#[CoversTrait(MemoizableKit::class)]
final class MemoizableTest extends TestCase {
	#[TestDox('will provide the same key for the same query with the same parameters')]
	public function testSameKey() {
		$query = new ExampleMemoizableQuery('hello', Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'));
		$query2 = new ExampleMemoizableQuery('hello', Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'));
		$this->assertEquals($query->getMemoKey(), $query2->getMemoKey());
	}

	#[TestDox('will provide a different key for the same query with different parameters')]
	public function testDifferentParams() {
		$query = new ExampleMemoizableQuery('hello', Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'));
		$query3 = new ExampleMemoizableQuery('hello', Identifier::fromString('062ad352-4321-47dc-aab1-313a94d11bcf'));
		$this->assertNotEquals($query->getMemoKey(), $query3->getMemoKey());
	}

	#[TestDox('will provide a different key for different query types regardless of parameters')]
	public function testDifferentClass() {
		$query = new ExampleMemoizableQuery('hello', Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'));
		$query2 = new readonly class(
			name: 'hello',
			id: Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'),
		) extends Query implements Memoizable {
			use MemoizableKit;
			public function __construct(public string $name, public Identifier $id) { parent::__construct(); }
		};
		$this->assertNotEquals($query->getMemoKey(), $query2->getMemoKey());
	}
}

