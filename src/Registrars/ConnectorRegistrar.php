<?php

namespace Smolblog\Core\Registrars;

use Smolblog\Core\{Connector, Registrar};

class ConnectorRegistrar {
	use Registrar;

	public static function register(Connector $connector = null, ?string $withSlug = null): void {
		$slug = $withSlug ?? $connector->slug();
		static::addToRegistry(object: $connector, slug: $slug);
	}

	public static function retrieve(string $slug = ''): ?Connector {
		return static::getFromRegistry(slug: $slug);
	}
}
