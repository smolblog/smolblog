<?php
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;

describe('Value::with', function() {
	it('creates a new object', function() {
		$first = new readonly class('world') extends Value {
			public function __construct(public string $hello) {}
		};
		$second = $first->with();

		expect($second)->toBeInstanceOf(get_class($first));
		expect($first->hello)->toBe($second->hello);
		expect($first)->not->toBe($second);
	});

	it('will replace the given fields', function() {
		$first = new readonly class('one', 'five') extends Value {
			public function __construct(public string $one, public string $three) {}
		};
		$second = $first->with(three: 'three');

		expect($first->one)->toBe('one');
		expect($second->one)->toBe('one');
		expect($first->three)->toBe('five');
		expect($second->three)->toBe('three');
	});

	it('will ignore private values', function() {
		$first = new readonly class('given', 'given') extends Value {
			public function __construct(public string $public = 'default', private string $private = 'default') {}
			public function getPrivate() { return $this->private; }
		};
		$second = $first->with();

		expect($first->public)->toBe('given');
		expect($second->public)->toBe('given');
		expect($first->getPrivate())->toBe('given');
		expect($second->getPrivate())->toBe('default');
	});

	it('will throw an exception on error', function () {
		$first = new readonly class('camelot') extends Value {
			public function __construct(public string $camelot) {}
		};
		$second = $first->with(itIsOnly: 'a model');
	})->throws(InvalidValueProperties::class);
});
