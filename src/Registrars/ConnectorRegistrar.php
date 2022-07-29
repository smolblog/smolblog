<?php

namespace Smolblog\Core\Registrars;

use Smolblog\Core\Connector;
use Smolblog\Core\Toolkits\RegistrarToolkit;

/**
 * Class to handle storing Connectors for use later.
 */
class ConnectorRegistrar {
	use RegistrarToolkit;

	/**
	 * Add a Connector instance to the registry. A slug can be provided, or it
	 * can default to the Connector's slug() method.
	 *
	 * @param Connector   $connector Connector object to store.
	 * @param string|null $withSlug  Short string to uniquely identify the object.
	 * @return void
	 */
	public static function register(Connector $connector, ?string $withSlug = null): void {
		$slug = $withSlug ?? $connector->slug();
		static::addToRegistry(object: $connector, slug: $slug);
	}

	/**
	 * Get the Connector corresponding to the given slug. Returns null if none is
	 * found in the registry.
	 *
	 * @param string $slug Identifier for the Connector to retrieve.
	 * @return Connector|null
	 */
	public static function retrieve(string $slug): ?Connector {
		return static::getFromRegistry(slug: $slug);
	}
}
