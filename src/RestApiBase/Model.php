<?php

namespace Smolblog\RestApiBase;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Smolblog\Framework\Objects\DomainModel;

class Model extends DomainModel {
	const SERVICES = [
		// Connector\AuthInit::class => [],
		Connector\AuthCallback::class => [],
	];

	public static function generateOpenApiSpec(): void {
		$endpoints = [];
		foreach (array_keys(self::SERVICES) as $endpoint) {
			if (!in_array(Endpoint::class, class_implements($endpoint))) {
				continue;
			}

			$classReflect = new ReflectionClass($endpoint);

			$endpoints[$endpoint] = [
				'config' => $endpoint::getConfiguration(),
				'class' => $classReflect,
				'response' => new ReflectionClass(strval($classReflect->getMethod('run')->getReturnType())),
			];

			print_r(
				array_map(
					fn($prop) => [$prop->getName(), strval($prop->getType()), $prop->getDocComment()],
					$endpoints[$endpoint]['response']->getProperties(ReflectionProperty::IS_PUBLIC)
				)
			);
		}
	}
}
