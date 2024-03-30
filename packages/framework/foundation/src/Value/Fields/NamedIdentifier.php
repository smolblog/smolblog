<?php

namespace Smolblog\Foundation\Value\Fields;

use Ramsey\Uuid\Uuid;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Throwable;

/**
 * A name-based (version 5) UUID.
 *
 * This is useful for creating deterministic identifiers, useful for sub-entities or intersection entities.
 */
readonly class NamedIdentifier extends Identifier {
	/**
	 * Standard namespace for creating Identifiers from a URL.
	 */
	public const NAMESPACE_URL = Uuid::NAMESPACE_URL;

	/**
	 * Standard namespace for creating Identifiers from a fully-qualified domain name.
	 */
	public const NAMESPACE_DOMAIN = Uuid::NAMESPACE_DNS;

	/**
	 * Create the UUID
	 *
	 * @throws InvalidValueProperties Thrown if the namespace is not a valid UUID.
	 *
	 * @param string $namespace UUID-formatted string to namespace the ID.
	 * @param string $name      String to build the UUID from.
	 */
	public function __construct(string $namespace, string $name) {
		try {
			parent::__construct(internal: Uuid::uuid5($namespace, $name));
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				message: "Could not create NamedIdentifier from namespace $namespace and name $name",
				previous: $e
			);
		}
	}
}
