<?php

namespace Smolblog\Foundation\v2\Value\Factories;

use DateTimeInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory as RamseyUuidFactory;
use Ramsey\Uuid\UuidInterface;
use Stringable;

/**
 * Central location for creating UUID objects.
 *
 * Is this an anti-pattern? Probably. But while it type-hints against Ramsey\Uuid, it could be implemented by anything.
 * Including a future version of Ramsey\Uuid or some future PSR standard.
 */
class UuidFactory {
	/**
	 * Internal instance of a Ramesy\Uuid\UuidFactory.
	 *
	 * Typehint should be replaced with UuidFactoryInterface when the interface gets ::uuid7 in version 5.
	 *
	 * @var RamseyUuidFactory
	 */
	private static RamseyUuidFactory $internal;

	private static function factory(): RamseyUuidFactory {
		self::$internal ??= new RamseyUuidFactory();
		return self::$internal;
	}

	/**
	 * Replace the instance of UuidFactory.
	 *
	 * @param RamseyUuidFactory $newSource Ramsey\Uuid-compatible factory.
	 * @return void
	 */
	public static function setSource(RamseyUuidFactory $newSource) {
		self::$internal = $newSource;
	}

	/**
	 * Standard namespace for creating Identifiers from a URL.
	 */
	public const NAMESPACE_URL = Uuid::NAMESPACE_URL;

	/**
	 * Standard namespace for creating Identifiers from a fully-qualified domain name.
	 */
	public const NAMESPACE_DOMAIN = Uuid::NAMESPACE_DNS;

	/**
	 * Create a random identifier (v4 UUID)
	 *
	 * @return UuidInterface
	 */
	public static function random(): UuidInterface {
		return self::factory()->uuid4();
	}

	/**
	 * Create a namespaced identifier (v5 UUID)
	 *
	 * @param string|UuidInterface $namespace Namespace for the identifier. Must be a valid UUID.
	 * @param string|Stringable    $name      The name to use for creating a UUID.
	 * @return UuidInterface
	 */
	public static function named(string|UuidInterface $namespace, string|Stringable $name): UuidInterface {
		return self::factory()->uuid5($namespace, \strval($name));
	}

	/**
	 * Create a timestamp-prefixed identifier (v7 UUID)
	 *
	 * @param DateTimeInterface|null $date Optional date to create the UUID with.
	 * @return UuidInterface
	 */
	public static function date(?DateTimeInterface $date = null): UuidInterface {
		return self::factory()->uuid7($date);
	}

	/**
	 * Deserialize a UUID from a standard string (e.g: 969726d0-ba27-4d35-b00b-5ec434201c94).
	 *
	 * @param string $serialized Standard-formatted UUID string.
	 * @return UuidInterface
	 */
	public static function fromString(string $serialized): UuidInterface {
		return self::factory()->fromString($serialized);
	}

	/**
	 * Deserialize a UUID from a byte-compressed string.
	 *
	 * @param string $serialized Byte-compressed UUID string.
	 * @return UuidInterface
	 */
	public static function fromByteString(string $serialized): UuidInterface {
		return self::factory()->fromBytes($serialized);
	}
}
