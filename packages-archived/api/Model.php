<?php

namespace Smolblog\Infrastructure;

use Smolblog\Foundation\DomainModel;

/**
 * Declared dependencies for the default infrastructure.
 *
 * You may override a few of these or omit this model entirely and add the services to your application's model.
 */
class Model extends DomainModel {
	public const AUTO_SERVICES = [
		Endpoint\EndpointRegistry::class,
		OpenApi\OpenApiGenerator::class,
	];
}
