<?php

namespace Smolblog\Framework\Foundation\Values;

use Override;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Smolblog\Framework\Foundation\StringableValue;
use Smolblog\Framework\Foundation\StringableValueKit;
use Smolblog\Framework\Foundation\Value;

/**
 * A unique identifier, or UUID.
 *
 * A 128-bit value that can be generated from a string, a time, or randomly. Used as the basis for Entity
 * identification. Creates version 4, 5, or 7 UUIDs. Do not instantiate this class directly; use DateIdentifier,
 * RandomIdentifier, or NamedIdentifier instead.
 *
 * Essentially a wrapper around Ramsey\Uuid.
 */
readonly class Identifier extends Value implements StringableValue {
	use StringableValueKit;

	/**
	 * Re-create an Identifier instance from a string representation.
	 *
	 * Not to be confused with the create* methods; this creates an instance from an existing ID's representation. The
	 * string is expected to be the standard Uuid format, such as "f68e15e6-a402-4b33-b6ae-84236d90d661".
	 *
	 * @param mixed $idString Uuid string to create instance from.
	 * @return static Identifier equal to the given string.
	 */
	public static function fromString(mixed $idString): static {
		return new self(internal: Uuid::fromString($idString));
	}

	/**
	 * Re-create an Identifier instance from a byte string representation.
	 *
	 * Not to be confused with the create* methods; this creates an instance from an existing ID's representation. A
	 * byte string is typed as a string but is in actuality the 16 bytes unencoded.
	 *
	 * @param string $byteString Uuid string to create instance from.
	 * @return self Identifier equal to the given string.
	 */
	public static function fromByteString(string $byteString): self {
		return new self(internal: Uuid::fromBytes($byteString));
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
