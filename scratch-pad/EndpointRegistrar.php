<?php

namespace Smolblog\Core\Registrars;

use Smolblog\Core\{Endpoint, Registrar};

/**
 * Registrar for Endpoints. Any endpoint registered here is assumed to be ready
 * and able to accept requests.
 */
class EndpointRegistrar {
	use Registrar;

	public static function register(Endpoint $endpoint = null, string $withSlug = ''): void {
		static::addToRegistry(object: $endpoint, slug: $withSlug);
	}

	public static function retrieve(string $slug = ''): ?Endpoint {
		return static::getFromRegistry(slug: $slug);
	}

	public static function retrieveAll(): array {
		return static::$registry;
	}
}
