<?php

namespace Smolblog\Foundation\Value\Fields;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Field;
use Smolblog\Foundation\Value\Traits\FieldKit;
use Throwable;

/**
 * A unique identifier, or UUID.
 *
 * A 128-bit value that can be generated from a string, a time, or randomly. Used as the basis for Entity
 * identification. Creates version 4, 5, or 7 UUIDs. Do not instantiate this class directly; use DateIdentifier,
 * RandomIdentifier, or NamedIdentifier instead.
 *
 * Essentially a wrapper around Ramsey\Uuid.
 */
readonly class Identifier extends Value implements Field {
	use FieldKit;

	/**
	 * Get an identifier that is all zeros (00000000-0000-0000-0000-000000000000).
	 *
	 * @return self
	 */
	public static function nil(): self {
		return self::fromString('00000000-0000-0000-0000-000000000000');
	}

	/**
	 * Re-create an Identifier instance from a string representation.
	 *
	 * Not to be confused with the create* methods; this creates an instance from an existing ID's representation. The
	 * string is expected to be the standard Uuid format, such as "f68e15e6-a402-4b33-b6ae-84236d90d661".
	 *
	 * @throws InvalidValueProperties Thrown if the string is not a valid UUID.
	 *
	 * @param string $idString Uuid string to create instance from.
	 * @return self Identifier equal to the given string.
	 */
	public static function fromString(string $idString): static {
		try {
			return new self(internal: Uuid::fromString($idString));
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				message: "Could not create Identifier from string $idString",
				previous: $e
			);
		}
	}

	/**
	 * Re-create an Identifier instance from a byte string representation.
	 *
	 * Not to be confused with the create* methods; this creates an instance from an existing ID's representation. A
	 * byte string is typed as a string but is in actuality the 16 bytes unencoded.
	 *
	 * @throws InvalidValueProperties Thrown if the string is not a valid UUID.
	 *
	 * @param string $byteString Uuid string to create instance from.
	 * @return self Identifier equal to the given string.
	 */
	public static function fromByteString(string $byteString): self {
		try {
			return new self(internal: Uuid::fromBytes($byteString));
		} catch (Throwable $e) {
			$idString = bin2hex($byteString);
			throw new InvalidValueProperties(
				message: "Could not create Identifier from string $idString",
				previous: $e
			);
		}
	}

	/**
	 * Create an Identifier from a Ramsey\Uuid. Not for general use.
	 *
	 * @param UuidInterface $internal Uuid instance to use.
	 */
	protected function __construct(
		private UuidInterface $internal
	) {
	}

	/**
	 * Get a string representation of this Identifier.
	 *
	 * @return string
	 */
	public function toString(): string {
		return $this->internal->toString();
	}

	/**
	 * Get the 16 bytes of this Identifier as an unencoded string.
	 *
	 * @return string
	 */
	public function toByteString(): string {
		return $this->internal->getBytes();
	}
}
