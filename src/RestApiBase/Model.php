<?php

namespace Smolblog\RestApiBase;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Smolblog\Framework\Objects\DomainModel;
use Smolblog\RestApiBase\Exceptions\BadRequest;
use Smolblog\RestApiBase\Exceptions\ErrorResponse;
use Smolblog\RestApiBase\Exceptions\NotFound;

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
			$runReflect = $classReflect->getMethod('run');
			$config = $endpoint::getConfiguration();
			$errors = self::getThrownResponses($runReflect->getDocComment());
			$descriptions = self::getDescription($classReflect->getDocComment());

			/*
			print_r(
				[
					'errors' => $data['errors'],
					'response' => array_map(
						fn($prop) => [
							'name' => $prop->getName(),
							'type' => strval($prop->getType()),
							'attributes' => array_map(fn($att) => $att->getArguments(), $prop->getAttributes(DataType::class) ?? [])
						],
						$data['response']->getProperties(ReflectionProperty::IS_PUBLIC)
					)
				]
			);
			*/

			$parameters = [
				...array_map(
					fn($key, $val) => [...self::processParam($key, $val), 'in' => 'path', 'required' => true],
					array_keys($config->pathVariables),
					array_values($config->pathVariables)
				),
				...array_map(
					fn($key, $val) => [...self::processParam($key, $val), 'in' => 'query'],
					array_keys($config->queryVariables),
					array_values($config->queryVariables)
				),
			];

			$endpoints[$config->route] = [
				strtolower($config->verb->value) => [
					'tags' => [ str_replace(__NAMESPACE__ . '\\', '', $classReflect->getNamespaceName()) ],
					'summary' => $descriptions[0],
					'description' => $descriptions[1],
					'operationId' => str_replace('\\', '', $endpoint),
					'parameters' => $parameters,
					'responses' => $errors,
				],
			];

		}//end foreach

		print_r($endpoints);
	}

	private static function getDescription(string $docblock): array {
		$summary = '';
		$description = '';

		foreach (explode("\n", $docblock) as $line) {
			if (str_starts_with('/**', $line) || str_contains($line, '*/')) {
				continue;
			}
			$procLine = ltrim($line, "* \t\n\r\0\x0B");

			if (empty($summary)) {
				$summary = $procLine;
				continue;
			}
			if (empty($description . $procLine)) {
				continue;
			}

			$description .= "$procLine\n";
		}

		return [$summary, $description];
	}

	private static function getThrownResponses(string $docblock): array {
		$matches = [];
		preg_match_all('/@throws\s+(\w+)\s+(.+)/', $docblock, $matches, PREG_SET_ORDER);

		$responses = [];
		foreach ($matches as $match) {
			$className = class_exists($match[1]) ? $match[1] : __NAMESPACE__ . '\\Exceptions\\' . $match[1];
			$description = $match[2];

			switch ($className) {
				case BadRequest::class:
					$responses[strval(400)] = ['description' => $description, 'content' => ErrorResponse::SCHEMA];
					break;
				case NotFound::class:
					$responses['404'] = ['description' => $description, 'content' => ErrorResponse::SCHEMA];
					break;
			}
		}

		return $responses;
	}

	private static function processParam(string $name, ParameterType $schema): array {
		return [
			'name' => $name,
			'required' => $schema->required,
			'schema' => $schema->schema(),
		];
	}
}
