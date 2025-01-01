<?php

namespace Smolblog\Infrastructure;

use ReflectionClass;
use ReflectionProperty;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\DomainModel;
use Smolblog\Infrastructure\Endpoint\EndpointConfiguration;

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
		$refClass = new ReflectionClass(EndpointConfiguration::class);

		echo "Class doc:\n";
		echo $refClass->getDocComment();

		echo "\n\n";

		foreach ($refClass->getProperties(ReflectionProperty::IS_PUBLIC) as $refProp) {
			echo "Property {$refProp->getName()} doc:\n";
			echo $refProp->getDocComment();

			echo "\n\n";
		}
	}
}
