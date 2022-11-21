<?php

namespace Smolblog\Framework;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Stringable;

/**
 * A unique identifier, or UUID.
 *
 * A 128-bit value that can be generated from a string, a time, or randomly. Used as the basis for Entity
 * identification. Creates version 4, 5, or 7 UUIDs.
 *
 * Essentially a wrapper around Ramsey\Uuid.
 */
class Identifier implements Stringable {
	/**
	 * Standard namespace for creating Identifiers from a URL.
	 */
	public const NAMESPACE_URL = Uuid::NAMESPACE_URL;

	/**
	 * Standard namespace for creating Identifiers from a fully-qualified domain name.
	 */
	public const NAMESPACE_DOMAIN = Uuid::NAMESPACE_DNS;

	/**
	 * Re-create an Identifier instance from a string representation.
	 *
	 * Not to be confused with the create* methods; this creates an instance from an existing ID's representation. The
	 * string is expected to be the standard Uuid format, such as "f68e15e6-a402-4b33-b6ae-84236d90d661".
	 *
	 * @param string $idString Uuid string to create instance from.
	 * @return static Identifier equal to the given string.
	 */
	public static function fromString(string $idString): static {
		return new static(internal: Uuid::fromString($idString));
	}

	/**
	 * Re-create an Identifier instance from a byte string representation.
	 *
	 * Not to be confused with the create* methods; this creates an instance from an existing ID's representation. A
	 * byte string is typed as a string but is in actuality the 16 bytes unencoded.
	 *
	 * @param string $byteString Uuid string to create instance from.
	 * @return static Identifier equal to the given string.
	 */
	public static function fromByteString(string $byteString): static {
		return new static(internal: Uuid::fromBytes($byteString));
	}

	/**
	 * Create a new random Identifier.
	 *
	 * Creates a version 4 UUID.
	 *
	 * @return static
	 */
	public static function createRandom(): static {
		return new static(internal: Uuid::uuid4());
	}

	/**
	 * Create a new Identifier from the given date (default now).
	 *
	 * Creates a version 7 UUID using the given date/time.
	 *
	 * @param DateTimeInterface $date Optional date to use to create the Identifier; defaults to now.
	 * @return static
	 */
	public static function createFromDate(DateTimeInterface $date = null): static {
		return new static(internal: Uuid::uuid7($date));
	}

	/**
	 * Create a new identifier from the given namespace and name.
	 *
	 * Creates a version 5 UUID using the given namespace and name. The namespace must be a valid UUID. It is
	 * recommended for classes using these to define their namespace as a public constant.
	 *
	 * @param string $namespace UUID string to differentiate the names for this domain.
	 * @param string $name      Unique name to generate the Identifier from.
	 * @return static
	 */
	public static function createFromName(string $namespace, string $name): static {
		return new static(internal: Uuid::uuid5($namespace, $name));
	}

	/**
	 * Create an Identifier from a Ramsey\Uuid. Not for general use.
	 *
	 * @param UuidInterface $internal Uuid instance to use.
	 */
	private function __construct(
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

	/**
	 * Get a string representation of this Identifier.
	 *
	 * Same as toString(), but in a PHP standard.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->toString();
	}
}
