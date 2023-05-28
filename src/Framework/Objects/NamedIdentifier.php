<?php

namespace Smolblog\Framework\Objects;

use Ramsey\Uuid\Uuid;

class NamedIdentifier extends Identifier {
	/**
	 * Standard namespace for creating Identifiers from a URL.
	 */
	public const NAMESPACE_URL = Uuid::NAMESPACE_URL;

	/**
	 * Standard namespace for creating Identifiers from a fully-qualified domain name.
	 */
	public const NAMESPACE_DOMAIN = Uuid::NAMESPACE_DNS;

	public function __construct(string $namespace, string $name) {
		parent::__construct(internal: Uuid::uuid5($namespace, $name));
	}
}
