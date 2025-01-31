<?php

namespace Smolblog\Infrastructure\OpenApi;

use BackedEnum;
use DateTimeInterface;
use JsonSerializable;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionEnum;
use ReflectionProperty;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Foundation\Value\Fields\{DateTimeField, Email, Identifier, Markdown, Url};
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\Field;
use stdClass;

/**
 * Register documented endpoints and create an OpenAPI spec.
 */
class OpenApiGenerator implements Registry {
	/**
	 * OpenAPI documented classes.
	 *
	 * @var class-string<OpenApiDocumentedEndpoint>[]
	 */
	private array $registry = [];

	/**
	 * Classes that need schemas generated.
	 *
	 * @var class-string[]
	 */
	private array $schemasNeeded = [];

	/**
	 * Get the interface this Registry tracks.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return OpenApiDocumentedEndpoint::class;
	}

	/**
	 * Accept the configuration for the registry.
	 *
	 * @param string[] $configuration Array of classes to register.
	 * @return void
	 */
	public function configure(array $configuration): void {
		$this->registry = $configuration;
	}

	/**
	 * Generate an OpenAPI spec with the configured endpoints and the given info.
	 *
	 * @param OpenApiSpecInfo $info General info.
	 * @return array
	 */
	public function generate(OpenApiSpecInfo $info): array {
		$this->schemasNeeded = [];

		$paths = [];
		foreach ($this->registry as $endpointClass) {
			$config = $endpointClass::getConfiguration();
			$spec = $endpointClass::getOpenApiSpec();

			$paths[$config->route] ??= [];
			$paths[$config->route][strtolower($config->verb->value)] = $spec->operation;

			array_push($this->schemasNeeded, ...$spec->referencedClasses);
		}

		$components = [];
		while (!empty($this->schemasNeeded)) {
			$schemaClass = array_shift($this->schemasNeeded);
			if (!isset($schemaClass)) {
				continue;
			}

			$schemaName = OpenApiUtils::makeAbbreviatedName($schemaClass);
			$components[$schemaName] ??= $this->componentSchemaFromClass($schemaClass);
		}

		$spec = ['title' => $info->title, 'version' => $info->version];

		return $spec;
	}

	/**
	 * Generate a component schema from the given class.
	 *
	 * @param class-string $className Class to reference.
	 * @return array
	 */
	public function componentSchemaFromClass(string $className): array {
		// If the class provides its own schema, use it.
		if (is_a($className, OpenApiDocumentedValue::class, allow_string: true)) {
			$schema = $className::getOpenApiSchema();
			array_push($this->schemasNeeded, ...$schema->referencedClasses);

			return $schema->schema;
		}

		// If the class is a BackedEnum, handle that.
		if (is_a($className, BackedEnum::class, allow_string: true)) {
			$refEnum = new ReflectionEnum($className);
			return [
				'type' => ($refEnum->getBackingType() === 'int') ? 'integer' : 'string',
				'enum' => \array_map(fn($refCase) => $refCase->getBackingValue(), $refEnum->getCases()),
			];
		}

		$reflect = new ReflectionClass($className);

		$props = [];
		foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
			$name = $prop->getName();
			$props[$name] = $this->typeFromProperty($prop);
		}

		$required = array_map(
			fn($refParam) => $refParam->getName(),
			array_filter(
				$reflect->getConstructor()?->getParameters() ?? [],
				fn($refParam) => !$refParam->isOptional()
			),
		);

		$schema = ['type' => 'object', 'properties' => $props];
		if (!empty($required)) {
			$schema['required'] = $required;
		}

		return $schema;
	}

	/**
	 * Turn a property into a schema.
	 *
	 * @throws CodePathNotSupported When an array doesn't have an ArrayType annotation.
	 *
	 * @param ReflectionProperty $prop Property to parse.
	 * @return array
	 */
	private function typeFromProperty(ReflectionProperty $prop): array {
		$typeName = ltrim(strval($prop->getType()), '?');

		// Handle some known types.
		switch ($typeName) {
			case 'bool':
				return ['type' => 'boolean'];
			case 'int':
				return ['type' => 'integer'];
			case 'float':
				return ['type' => 'number'];
			case 'string':
				return ['type' => 'string'];
			case Identifier::class:
				return ['type' => 'string', 'format' => 'uuid'];
			case DateTimeField::class:
				return ['type' => 'string', 'format' => 'date-time'];
			case Email::class:
				return ['type' => 'string', 'format' => 'email'];
			case Url::class:
				return ['type' => 'string', 'format' => 'uri'];
		}

		// If it's a Field, then it serializes to/from a string, so use that.
		if (is_a($typeName, Field::class, allow_string: true)) {
			return ['type' => 'string'];
		}

		// Handle arrays.
		if ($typeName === 'array') {
			$attributeReflections = $prop->getAttributes(ArrayType::class, ReflectionAttribute::IS_INSTANCEOF);
			$arrayType = ($attributeReflections[0] ?? null)?->newInstance() ?? null;
			if (!isset($arrayType)) {
				$arrayType = new ArrayType(ArrayType::NO_TYPE);
			}

			$itemSchema = match (true) {
				$arrayType->type === ArrayType::NO_TYPE => new stdClass(),
				$arrayType->isBuiltIn() => OpenApiUtils::builtInArrayTypeSchema($arrayType->type),
				default => $this->enqueueClassAndGetRef($arrayType->type)
			};

			// Check for associative arrays (maps) and handle those correctly.
			if ($arrayType->isMap) {
				return ['type' => 'object', 'additionalProperties' => $itemSchema];
			}

			return ['type' => 'array', 'items' => $itemSchema];
		}//end if

		return $this->enqueueClassAndGetRef($typeName);
	}

	/**
	 * Create a reference to a schema and enqueue the class to be processed.
	 *
	 * @param string $className Class to handle.
	 * @return array
	 */
	private function enqueueClassAndGetRef(string $className): array {
		array_push($this->schemasNeeded, $className);
		return ['$ref' => '#/components/schemas/' . OpenApiUtils::makeAbbreviatedName($className)];
	}
}
