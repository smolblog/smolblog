<?php
use Smolblog\Foundation\Value\Messages\Query;

describe('Query::setResults and Query::results', function() {
	it('can store and retrieve a result', function() {
		$query = new readonly class('say') extends Query {
			public function __construct(public string $name) {
				parent::__construct();
			}
		};

		$query->setResults('hello');
		expect($query->results())->toBe('hello');
	});
});
