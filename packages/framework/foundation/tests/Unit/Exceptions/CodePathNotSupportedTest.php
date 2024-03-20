<?php
use Smolblog\Framework\Foundation\Exceptions\CodePathNotSupported;

it('can be created with a message', function() {
	$exception = new CodePathNotSupported('message');
	expect($exception->getMessage())->toBe('message');
});

it('can be created with a code', function() {
	expect(new CodePathNotSupported(code: 123))->toBeInstanceOf(CodePathNotSupported::class);
});

it('can be created with a previous exception', function() {
	$previous = new Exception('previous');
	$exception = new CodePathNotSupported(previous: $previous);
	expect($exception->getPrevious())->toBe($previous);
});

it('can be created with a location and message', function() {
	$exception = new CodePathNotSupported('message', location: 'location');
	expect($exception->getMessage())->toBe('In location: message');
});

it('can be created with a location and no message', function() {
	$exception = new CodePathNotSupported(location: 'location');
	expect($exception->getMessage())->toBe('In location: The code path is not supported.');
});

it('can be created without a message', function() {
	$exception = new CodePathNotSupported();
	expect($exception->getMessage())->toBe('The code path is not supported.');
});
