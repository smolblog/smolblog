<?php

namespace Smolblog\Test\Generators;

use Composer\Script\Event;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Resolvers\SchemaResolver;
use Opis\JsonSchema\SchemaLoader;
use Opis\JsonSchema\Uri;
use Opis\JsonSchema\Validator;
use Swaggest\JsonSchema\Schema;

class ValueObjectsFromJsonSchema {
	public static function generateFromFile(Event $event) {
		$args = $event->getArguments();

		$namespace = $args[0];
		$file = $args[1];

		if (!\file_exists($file)) {
			echo "File $file does not exist.";
			exit(1);
		}

		$schemaFile = \file_get_contents($file);
		if (!\json_validate($schemaFile)) {
			echo "File $file is not valid JSON.";
			exit(1);
		}

		$schema = \json_decode($schemaFile, associative: true);

		self::generate($namespace, $schema);
	}

	public static function test() {
		// echo "Testing\n";
		$baseSchema = Schema::import(json_decode(\file_get_contents(__DIR__ . '/testschema.json')));
		$appNs = 'Smolblog\\Test\\Generated';

		$app = new \Swaggest\PhpCodeBuilder\App\PhpApp();
		$app->setNamespaceRoot($appNs, '.');


		$builder = new \Swaggest\PhpCodeBuilder\JsonSchema\PhpBuilder();
		$builder->buildSetters = true;
		$builder->makeEnumConstants = true;

		// $classes = [];

		// $builder->classCreatedHook = new \Swaggest\PhpCodeBuilder\JsonSchema\ClassHookCallback(
    // function (\Swaggest\PhpCodeBuilder\PhpClass $class, $path, $schema) use ($app, $appNs) {
    //     $desc = '';
    //     if ($schema->title) {
    //         $desc = $schema->title;
    //     }
    //     if ($schema->description) {
    //         $desc .= "\n" . $schema->description;
    //     }
    //     if ($fromRefs = $schema->getFromRefs()) {
    //         $desc .= "\nBuilt from " . implode("\n" . ' <- ', $fromRefs);
    //     }

    //     $class->setDescription(trim($desc));

    //     $class->setNamespace($appNs);
    //     if ('#' === $path) {
    //         $class->setName('User'); // Class name for root schema
    //     } elseif (strpos($path, '#/definitions/') === 0) {
    //         $class->setName(\Swaggest\PhpCodeBuilder\PhpCode::makePhpClassName(
    //             substr($path, strlen('#/definitions/'))));
    //     }
		// 		// print_r($class);
    //     $app->addClass($class);
		// 	}
		// );

		// $builder->getType($baseSchema);

		print_r([
			'type' => $baseSchema->type,
		]);
		exit;

		foreach ([] as $genClass) {
			$classDef = $genClass->class;
			echo "{$classDef->getName()}.php\n";
			echo "  namespace {$classDef->getNamespace()};\n";
			echo "  Properties:\n";
			foreach ($classDef->getProperties() as $prop) {
				$arrayType = '';
				$var = $prop->getNamedVar();
				$type = $var->renderArgumentType();
				if (empty($type)) {
					$type = $var->renderPhpDocValue();
					$hasArrayType = strpos($type, '[]|');
					if ($hasArrayType) {
						$parsedType = \substr($type, 0, $hasArrayType);
						$arrayType = "#[ArrayType({$parsedType})] ";
						$type = substr($type, $hasArrayType + 3);
					}
				}
				echo "    {$arrayType}public {$type} {$var->getName()}{$var->renderDefault()},\n";
			}
		}
	}

	public static function testMine() {
		self::generate(
			'Smolblog\\Test\\Generated',
			json_decode(\file_get_contents(__DIR__ . '/testschema.json'), associative: true),
			'Profile',
		);
		$data = <<<'JSON'
{
    "name": "John Doe",
    "age": 31,
    "email": "john@example.com",
    "website": null,
    "location": {
        "country": "US",
        "address": "Sesame Street, no. 5"
    },
    "available_for_hire": true,
    "interests": ["php", "html", "css", "javascript", "programming", "web design"],
    "skills": [
        {
            "name": "HTML",
            "value": 100
        },
        {
            "name": "PHP",
            "value": 55
        },
        {
            "name": "CSS",
            "value": 99.5
        },
        {
            "name": "JavaScript",
            "value": 75
        }
    ]
}
JSON;
	}

	public static function generate(string $namespace, array $schema, string $rootObjectName) {
		$objectsToCreate = [];

	}

	private static function makeObject(array $properties, string $name, array &$objects): string {
		$className = \ucfirst($name);
		if (isset($objects[$className])) {
			echo "Duplicate key $className";
			exit(1);
		}

		$objData = [];
		foreach ($properties as $propName => $propDef) {
			$objData[$propName] = self::makeProperty($propDef, $propName, $objects);
		}
		$objects[$className] = $objData;
		return $className;
	}

	private static function makeProperty(array $definition, string $name, array &$objects): string {

	}
}
