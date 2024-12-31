<?php

namespace Smolblog\Infrastructure\Endpoint;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Foundation\Service\Registry\RegistryKit;

/**
 * Collect Endpoints and register them.
 */
class EndpointRegistry implements Registry {
	use RegistryKit;

	/**
	 * This registry registers Endpoint services.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return Endpoint::class;
	}

	/**
	 * Construct the service.
	 *
	 * @param ContainerInterface $container Dependency Injection container.
	 */
	public function __construct(private ContainerInterface $container) {
	}

	/**
	 * Debug endpoints.
	 *
	 * @return array
	 */
	public function getEndpoints(): array {
		return $this->configs;
	}

	/**
	 * Get the available endpoints as an OpenAPI `paths` section.
	 *
	 * @return array
	 */
	public function getOpenApiPaths(?string $authScheme = null): array {
		$keys = array_keys($this->configs);
		$paths = [];
		$schemas = [];

		foreach ($keys as $key) {
			$config = $this->configs[$key];
			$docs = is_a($this->library[$key], DocumentedEndpoint::class, allow_string: true) ?
				($this->library[$key])::getDocumentation() : null;

			$this->makePath($config, $docs, $paths, $schemas, $authScheme ?? 'unknown');
		}
		return [];
	}

	private function makePath(
		EndpointConfiguration $config,
		?EndpointDocumentation $docs,
		array &$paths,
		array &$schemas,
		string $authScheme
	) {
		$def = [
			'tags' => $docs?->tags,
			'summary' => $docs?->oneline,
			'description' => $docs?->longform?->toString(),
			'operationId' => $config->key,
		];
		if (isset($config->scope)) {
			$def['security'] = [
				$authScheme => [$config->scope],
			];
		}

		$paths[$config->route] ??= [];
		$paths[$config->route][\strtolower(\strval($config->verb))] = [];
	}

	private function schemaFromClass(string $className): array {
		return [];
	}
}
