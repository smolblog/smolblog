<?php

namespace Smolblog\Framework\Objects;

use Ramsey\Uuid\Uuid;

/**
 * A name-based (version 5) UUID.
 *
 * This is useful for creating deterministic identifiers, useful for sub-entities or intersection entities.
 */
class NamedIdentifier extends Identifier {
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
	 * @param string $namespace UUID-formatted string to namespace the ID.
	 * @param string $name      String to build the UUID from.
	 */
	public function __construct(string $namespace, string $name) {
		parent::__construct(internal: Uuid::uuid5($namespace, $name));
	}
}
