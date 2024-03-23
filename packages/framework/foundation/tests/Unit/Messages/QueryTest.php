<?php
use Smolblog\Framework\Foundation\Messages\Query;

it('can store and retrieve a result', function() {
	$query = new readonly class('say') extends Query {
		public function __construct(public string $name) {
			parent::__construct();
		}
	};

	$query->setResults('hello');
	expect($query->results())->toBe('hello');
});
