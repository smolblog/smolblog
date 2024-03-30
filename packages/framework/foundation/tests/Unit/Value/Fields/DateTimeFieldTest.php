<?php

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\DateTimeField;

describe('DateTimeField::__construct', function() {
	it('can be created with a string', function() {
		$date = new DateTimeField(datetime:'2022-02-22 22:22:22');
		expect($date)->toBeInstanceOf(DateTimeField::class);
		expect($date->object->format(DateTimeInterface::RFC3339_EXTENDED))->toBe('2022-02-22T22:22:22.000+00:00');
	});

	it('can be created with a string and a timezone', function() {
		$date = new DateTimeField(datetime:'2022-02-22 22:22:22', timezone: new DateTimeZone('America/New_York'));
		expect($date)->toBeInstanceOf(DateTimeField::class);
		expect($date->object->format(DateTimeInterface::RFC3339_EXTENDED))->toBe('2022-02-22T22:22:22.000-05:00');
	});

	it('can be created with a DateTime object', function() {
		$date = new DateTimeField(object: new DateTimeImmutable('2022-02-22 22:22:22'));
		expect($date)->toBeInstanceOf(DateTimeField::class);
		expect($date->object->format(DateTimeInterface::RFC3339_EXTENDED))->toBe('2022-02-22T22:22:22.000+00:00');
	});
});

describe('DateTimeField::toString', function() {
	$dateString = '2022-02-22T22:22:22.000+00:00';
	$dateObject = new DateTimeField(datetime: $dateString);

	it('will serialize to a string', fn() =>
		expect($dateObject->toString())->toBe($dateString)
	);
});

describe('DateTimeField::fromString', function() {
	$dateString = '2022-02-22T22:22:22.000+00:00';
	$dateObject = new DateTimeField(datetime: $dateString);

	it('will deserialize from a string', fn() =>
		expect(DateTimeField::fromString($dateString)->toString())->toMatchValue($dateObject)
	);

	it('will throw an exception when the serialized string is invalid', function() {
		$badDateString = '2022-02-22T25:22:22.000+00:00';
		expect(fn() => DateTimeField::fromString($badDateString))->toThrow(InvalidValueProperties::class);
	});
});
