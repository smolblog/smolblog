<?php

use Smolblog\Framework\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Framework\Foundation\Values\DateTime;

it('can be created with a string', function() {
	$date = new DateTime(datetime:'2022-02-22 22:22:22');
	expect($date)->toBeInstanceOf(DateTime::class);
	expect($date->object->format(DateTimeInterface::RFC3339_EXTENDED))->toBe('2022-02-22T22:22:22.000+00:00');
});

it('can be created with a string and a timezone', function() {
	$date = new DateTime(datetime:'2022-02-22 22:22:22', timezone: new DateTimeZone('America/New_York'));
	expect($date)->toBeInstanceOf(DateTime::class);
	expect($date->object->format(DateTimeInterface::RFC3339_EXTENDED))->toBe('2022-02-22T22:22:22.000-05:00');
});

it('can be created with a DateTime object', function() {
	$date = new DateTime(object: new DateTimeImmutable('2022-02-22 22:22:22'));
	expect($date)->toBeInstanceOf(DateTime::class);
	expect($date->object->format(DateTimeInterface::RFC3339_EXTENDED))->toBe('2022-02-22T22:22:22.000+00:00');
});

it('will serialize to and deserialize from a string', function() {
	$dateString = '2022-02-22T22:22:22.000+00:00';
	$dateObject = new DateTime(datetime: $dateString);

	expect($dateObject->toString())->toBe($dateString);
	expect($dateObject->toArray())->toBe($dateString);

	expect(DateTime::fromString($dateString)->toString())->toMatchValue($dateObject);
	expect(DateTime::fromArray($dateString)->toString())->toMatchValue($dateObject);
});

it('will throw an exception when the serialized string is invalid', function() {
	$dateString = '2022-02-22T25:22:22.000+00:00';
	expect(fn() => DateTime::fromString($dateString))->toThrow(InvalidValueProperties::class);
});
