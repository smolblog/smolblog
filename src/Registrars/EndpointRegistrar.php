<?php

namespace Smolblog\Core\Registrars;

use Smolblog\Core\{Endpoint, Registrar};

class EndpointRegistrar {
	use Registrar;

	public static function register(Endpoint $object = null, string $withSlug = ''): void {
		static::addToRegistry(object: $object, slug: $withSlug);
	}

	public static function retrieve(string $slug = ''): ?Endpoint {
		return static::getFromRegistry(slug: $slug);
	}

	public static function retrieveAll(): array {
		return static::$registry;
	}
}
