<?php

namespace Smolblog\Infrastructure;

use ReflectionClass;
use ReflectionProperty;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Value\Http\HttpVerb;
use Smolblog\Infrastructure\Endpoint\EndpointConfiguration;
use Smolblog\Infrastructure\OpenApi\OpenApiGenerator;
use Smolblog\Infrastructure\OpenApi\OpenApiSpecInfo;

/**
 * Declared dependencies for the default infrastructure.
 *
 * You may override a few of these or omit this model entirely and add the services to your application's model.
 */
class Model extends DomainModel {
	public const AUTO_SERVICES = [
		Endpoint\EndpointRegistry::class,
	];

	public static function scratchpad() {
		$gen = new OpenApiGenerator();
		echo \json_encode($gen->componentSchemaFromClass(HttpVerb::class), JSON_PRETTY_PRINT);
	}
}
