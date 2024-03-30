<?php

use Smolblog\Framework\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Foundation\Value\Messages\Query;
use Smolblog\Framework\Foundation\Value\Traits\Memoizable;
use Smolblog\Framework\Foundation\Value\Traits\MemoizableKit;

readonly class TestMemoizableQuery extends Query implements Memoizable {
	use MemoizableKit;
	public function __construct(public string $name, public Identifier $id) {
		parent::__construct();
	}
}

it('will provide the same key for the same query with the same parameters', function() {
	$query = new TestMemoizableQuery('hello', Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'));
	$query2 = new TestMemoizableQuery('hello', Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'));
	expect($query->getMemoKey())->toBe($query2->getMemoKey());

	$query3 = new TestMemoizableQuery('hello', Identifier::fromString('062ad352-4321-47dc-aab1-313a94d11bcf'));
	expect($query->getMemoKey())->not->toBe($query3->getMemoKey());
});

it('will provide a different key for different query types regardless of parameters', function() {
	$query = new TestMemoizableQuery('hello', Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'));
	$query2 = new readonly class(
		name: 'hello',
		id: Identifier::fromString('fb0914b3-0224-4150-bd4b-2934aaddf9be'),
	) extends Query implements Memoizable {
		use MemoizableKit;
		public function __construct(public string $name, public Identifier $id) {
			parent::__construct();
		}
	};
	expect($query->getMemoKey())->not->toBe($query2->getMemoKey());
});