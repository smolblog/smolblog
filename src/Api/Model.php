<?php

namespace Smolblog\Api;

use DateTimeInterface;
use Exception;
use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;
use Smolblog\Framework\Objects\DomainModel;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\ErrorResponse;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Core\Connector\Services\AuthRequestStateRepo;
use Smolblog\Core\Connector\Services\ConnectorRegistry;
use Smolblog\Core\Content\Types\Reblog\ExternalContentService;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Domain model for the API.
 *
 * Also contains a script to create an OpenAPI (Swagger) spec from the given endpoints.
 *
 * @codeCoverageIgnore
 */
class Model extends DomainModel {
	public const SERVICES = [
		Connector\AuthInit::class => [
			'bus' => MessageBus::class,
			'connectors' => ConnectorRegistry::class,
			'env' => ApiEnvironment::class,
		],
		Connector\AuthCallback::class => [
			'bus' => MessageBus::class,
			'connectors' => ConnectorRegistry::class,
			'authRepo' => AuthRequestStateRepo::class,
		],
		Connector\ChannelLink::class => ['bus' => MessageBus::class],
		Connector\DeleteConnection::class => ['bus' => MessageBus::class],
		Connector\RefreshChannels::class => ['bus' => MessageBus::class],
		Connector\UserConnections::class => ['bus' => MessageBus::class],
		Connector\SiteAndAvailableChannels::class => ['bus' => MessageBus::class],

		Content\ListContent::class => ['bus' => MessageBus::class],
		Content\GetReblog::class => ['bus' => MessageBus::class],
		Content\CreateReblog::class => ['bus' => MessageBus::class],
		Content\UpdateReblog::class => ['bus' => MessageBus::class],
		Content\DeleteReblog::class => ['bus' => MessageBus::class],
		Content\GetStatus::class => ['bus' => MessageBus::class],
		Content\CreateStatus::class => ['bus' => MessageBus::class],
		Content\UpdateStatus::class => ['bus' => MessageBus::class],
		Content\DeleteStatus::class => ['bus' => MessageBus::class],

		Preview\PreviewEmbed::class => ['embed' => ExternalContentService::class],
		Preview\PreviewMarkdown::class => ['md' => SmolblogMarkdown::class],

		Server\Base::class => ['env' => ApiEnvironment::class],
		Server\Spec::class => ['env' => ApiEnvironment::class],

		Site\GetSettings::class => ['bus' => MessageBus::class],
		Site\UpdateSettings::class => ['bus' => MessageBus::class],
		Site\GetUsers::class => ['bus' => MessageBus::class],
		Site\UpdateUserPermissions::class => ['bus' => MessageBus::class],

		User\GetMyProfile::class => ['bus' => MessageBus::class],
		User\GetMySites::class => ['bus' => MessageBus::class],
		User\UpdateMyProfile::class => ['bus' => MessageBus::class],
	];

	/**
	 * Store schemas for reflected classes.
	 *
	 * These will be stored in the `components` section of the spec and referenced elsewhere.
	 *
	 * @var array
	 */
	private static array $schemaCache = [];

	/**
	 * Print the generated spec to standard output.
	 *
	 * @return void
	 */
	public static function printSpec(): void {
		echo json_encode(self::generateOpenApiSpec(), JSON_PRETTY_PRINT);
	}

	/**
	 * Create a JSON-formatted OpenAPI spec from the endpoints.
	 *
	 * @param string $apiBase URL base for the endpoints.
	 * @return array
	 */
	public static function generateOpenApiSpec(string $apiBase = null): array {
		$endpoints = [];
		foreach (array_keys(self::SERVICES) as $endpoint) {
			if (!in_array(Endpoint::class, class_implements($endpoint))) {
				continue;
			}

			$classReflect = new ReflectionClass($endpoint);
			$runReflect = $classReflect->getMethod('run');
			$config = $endpoint::getConfiguration();
			$responses = self::getThrownResponses($runReflect->getDocComment());
			$descriptions = self::getDescription($classReflect->getDocComment());

			$responseReturn = $runReflect->getReturnType();
			$responseReturnTypes = [];
			if (get_class($responseReturn) === ReflectionUnionType::class) {
				$responseReturnTypes = array_map(fn($type) => $type->getName(), $responseReturn->getTypes());
			} else {
				$responseReturnTypes[] = $responseReturn->getName();
			}

			foreach ($responseReturnTypes as $responseClassName) {
				switch ($responseClassName) {
					case SuccessResponse::class:
						$responses[204] = [
							'description' => 'Successful response',
						];
						break;

					case RedirectResponse::class:
						$responses[302] = [
							'description' => 'Redirect'
						];
						break;

					default:
						$responses[200] = [
							'description' => 'Successful response',
							'content' => [
								'application/json' => [
									'schema' => self::buildSuccessResponse($responseClassName, $config->responseShape)
								],
							],
						];
						break;
				}//end switch
			}//end foreach

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
			if (empty($parameters)) {
				$parameters = null;
			}

			$body = null;
			if (class_exists($config->bodyClass ?? '')) {
				$body = [
					'required' => true,
					'content' => ['application/json' => ['schema' => self::makeSchemaFromClass($config->bodyClass)]],
				];
			}

			$security = [];
			if (!empty($config->requiredScopes)) {
				$security[] = ['smolAuth' => array_map(fn($s) => $s->value, $config->requiredScopes)];
				$security[] = ['wpAuth' => []];
			}

			$endpoints[$config->route] = [
				strtolower($config->verb->value) => array_filter([
					'tags' => [ str_replace(__NAMESPACE__ . '\\', '', $classReflect->getNamespaceName()) ],
					'summary' => $descriptions[0],
					'description' => $descriptions[1],
					'operationId' => self::makeAbbreviatedName($endpoint),
					'security' => $security,
					'parameters' => $parameters,
					'requestBody' => $body,
					'responses' => $responses,
				], fn($i) => isset($i)),
			];
		}//end foreach

		$fullSchema = [
			'openapi' => '3.0.3',
			'info' => [
				'title' => 'Smolblog Core API',
				'version' => 'dev-main',
				'description' => <<<EOF
				Preferred way of interacting with a Smolblog server.

				The REST API is a first-class method—perhaps the only method!—of interacting with a server. It is the
				backbone of the dashboard web app and powers the server-to-server communication.
				EOF,
				'contact' => [
					'email' => 'dev@smolblog.org',
					'url' => 'https://smolblog.org/',
					'name' => 'The Smolblog Project',
				],
			],
			'externalDocs' => [
				'description' => 'All Smolblog project documentation',
				'url' => 'https://docs.smolblog.org/',
			],
			'paths' => $endpoints,
			'components' => [
				'schemas' => self::$schemaCache,
				'securitySchemes' => [
					'wpAuth' => [
						'type' => 'http',
						'scheme' => 'basic',
					],
					'smolAuth' => [
						'type' => 'oauth2',
						'description' => 'Gain user credentials without needing to store their password.',
						'flows' => [
							'authorizationCode' => [
								'authorizationUrl' => '/oauth/authorize',
								'tokenUrl' => '/oauth/token',
								'refreshUrl' => '/oauth/refresh',
								'scopes' => [
									'read' => 'Retrieve information from the API.',
									'write' => 'Make changes to a user\'s content or site.'
								],
							],
							'implicit' => [
								'authorizationUrl' => '/oauth/authorize',
								'refreshUrl' => '/oauth/refresh',
								'scopes' => [
									'read' => 'Retrieve information from the API.',
									'write' => 'Make changes to a user\'s content or site.'
								],
							]
						],
					],
				],
			],
		];

		if (!empty($apiBase)) {
			$fullSchema['servers'] = [
				['url' => $apiBase]
			];
		}

		return $fullSchema;
	}

	/**
	 * Get a description and summary from an endpoint class' docblock.
	 *
	 * @param string $docblock Unparsed docblock.
	 * @return array Summary on index 0, full description on index 1.
	 */
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

	/**
	 * Get error responses from the endpoint's run method's `throws` declarations.
	 *
	 * @param string $docblock Unparsed run method docblock.
	 * @return array
	 */
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

	/**
	 * Turn a parameter entry into a schema.
	 *
	 * @param string        $name   Name of the parameter.
	 * @param ParameterType $schema ParameterType for the parameter.
	 * @return array
	 */
	private static function processParam(string $name, ParameterType $schema): array {
		return [
			'name' => $name,
			'required' => $schema->required,
			'schema' => $schema->schema(),
		];
	}

	/**
	 * Build a response from config or return type.
	 *
	 * @throws Exception If class and shape are both null.
	 *
	 * @param string|null        $class Class returned by the endpoint's run() method.
	 * @param ParameterType|null $shape ParameterType describing the response; higher priority than $class.
	 * @return array
	 */
	private static function buildSuccessResponse(?string $class, ?ParameterType $shape): array {
		if (isset($shape)) {
			return $shape->schema();
		}

		if (!isset($class)) {
			throw new Exception("Need either a shape or class!");
		}

		return self::makeSchemaFromClass($class);
	}

	/**
	 * Make a schema from a class and return a reference.
	 *
	 * Stores the schema in self::$schemaCache and returns a reference to that schema.
	 *
	 * @param string $className Fully-qualified class name to reflect.
	 * @return array Reference to the class' schema.
	 */
	private static function makeSchemaFromClass(string $className): array {
		$compressedName = self::makeAbbreviatedName($className);
		if (isset(self::$schemaCache[$compressedName])) {
			return ['$ref' => '#/components/schemas/' . $compressedName];
		}

		$reflect = new ReflectionClass($className);
		$props = [];
		$required = [];
		foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
			$name = $prop->getName();
			$atts = $prop->getAttributes(ParameterType::class);

			if (isset($atts[0])) {
				$type = new ParameterType(...$atts[0]->getArguments());
				if ($type->required) {
					$required[] = $name;
				}

				$props[$name] = $type->schema();
				continue;
			}

			if (!$prop->getType()->allowsNull()) {
				$required[] = $name;
			}

			$props[$name] = self::typeFromProperty($prop);
		}//end foreach

		self::$schemaCache[$compressedName] = ['type' => 'object', 'properties' => $props, 'required' => $required];
		return ['$ref' => '#/components/schemas/' . $compressedName];
	}

	/**
	 * Turn a property or string into a schema.
	 *
	 * @param ReflectionProperty|string $prop Property or type name to parse.
	 * @return array
	 */
	private static function typeFromProperty(ReflectionProperty|string $prop): array {
		$typeName = is_string($prop) ? $prop : ltrim(strval($prop->getType()), '?');

		if (class_exists($typeName)) {
			switch ($typeName) {
				// Throw in some known types.
				case Identifier::class:
					return ParameterType::identifier()->schema();
				case DateTimeInterface::class:
					return ParameterType::dateTime()->schema();
			}

			return self::makeSchemaFromClass($typeName);
		}

		// Assuming this is a primitive type; just pass it along.
		if ($typeName === 'bool') {
			$typeName = 'boolean';
		}

		if ($typeName === 'array') {
			$arrayTypeAttribute = $prop->getAttributes(ArrayType::class);
			if ($arrayTypeAttribute[0]?->getArguments() !== null) {
				$arrayType = $arrayTypeAttribute[0]->getArguments()[0];
				if (is_array($arrayType)) {
					return [
						'type' => 'array',
						'items' => $arrayType,
					];
				}

				return [
					'type' => 'array',
					'items' => self::typeFromProperty($arrayType),
				];
			}
		}

		return ['type' => $typeName];
	}

	/**
	 * Make an OpenAPI-compatible name from a class.
	 *
	 * @param string $className Fully-qualified class name.
	 * @return string
	 */
	public static function makeAbbreviatedName(string $className): string {
		return str_replace('\\', '', str_replace(__NAMESPACE__, '', $className));
	}
}
