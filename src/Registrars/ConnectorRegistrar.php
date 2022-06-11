<?php

namespace Smolblog\Core\Registrars;

use Smolblog\Core\{ConnectionProvider, Registrar};

class ConnectorRegistrar {
	use Registrar;

	public static function register(Connector $object = null, string $withSlug = ''): void {
		static::addToRegistry(object: $object, slug: $withSlug);
	}

	public static function retrieve(string $slug = ''): ?Connector {
		return static::getFromRegistry(slug: $slug);
	}
}
