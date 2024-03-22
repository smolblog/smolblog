<?php
use Smolblog\Framework\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Framework\Foundation\Values\{Identifier, DateIdentifier, NamedIdentifier, RandomIdentifier};
use Smolblog\Framework\Foundation\Values\DateTime;

it('can be a random identifier', function() {
	expect(new RandomIdentifier())->toBeInstanceOf(Identifier::class);
});

describe('a date identifier', function() {
	it('can be created with a specfic date', function() {
		$date = new DateTimeImmutable('2022-02-22 22:22:22');
		expect(new DateIdentifier($date))->toBeInstanceOf(Identifier::class);
	});

	it('can be created with a DateTime object', function() {
		$date = new DateTime('2022-02-22 22:22:22');
		expect(new DateIdentifier($date))->toBeInstanceOf(Identifier::class);
	});

	it('will be created with the current date by default', function() {
		expect(new DateIdentifier())->toBeInstanceOf(Identifier::class);
	});
});

describe('a named identifier', function() {
	it('can be created with a namespace and name', function() {
		expect(new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, 'https://smol.blog/post/123'))
			->toBeInstanceOf(Identifier::class);
	});

	test('the namespace must be a UUID string', function() {
		expect(fn() => new NamedIdentifier('not-a-uuid', 'https://smol.blog/post/123'))
			->toThrow(InvalidArgumentException::class);
	});

	it('is created deterministically', function() {
		$id1 = new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, 'https://smol.blog/post/123');
		$id2 = new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, 'https://smol.blog/post/123');
		expect($id1)->toEqual($id2);
	});
});

it('will serialize to and deserialize from a string', function() {
	$idString = '10a353e4-0ccf-5f74-a77b-067262bfc588';
	$idObject = new NamedIdentifier(NamedIdentifier::NAMESPACE_URL, 'https://smol.blog/post/123');

	expect($idObject)->toBeInstanceOf(Identifier::class);
	expect($idObject->toString())->toBe($idString);
	expect($idObject->serialize())->toBe($idString);

	expect($idString)->toBeUuid();
	expect(Identifier::fromString($idString)->toString())->toMatchValue($idObject);
	expect(Identifier::deserialize($idString)->toString())->toMatchValue($idObject);
});

it('will serialize to and deserialize from a byte string', function() {
	$idString = 'b6520d39-66e5-4ff7-b799-5a9674b17502';
	$byteString = hex2bin('b6520d3966e54ff7b7995a9674b17502');

	expect($idString)->toMatchValue(Identifier::fromByteString($byteString));
	expect($byteString)->toMatchValue(Identifier::fromString($idString)->toByteString());
});

it('will throw an exception when the serialized string is invalid', function() {
	$idString = '10a353e4-0ccf-5f74-a77b-067262bfc5888';
	expect(fn() => Identifier::fromString($idString))->toThrow(InvalidValueProperties::class);

	$byteString = hex2bin('10a353e40ccf5f74a77b067262bfc58888');
	expect(fn() => Identifier::fromByteString($byteString))->toThrow(InvalidValueProperties::class);
});
