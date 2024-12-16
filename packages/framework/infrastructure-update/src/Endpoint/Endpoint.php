<?php

namespace Smolblog\Infrastructure\Endpoint;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smolblog\Foundation\Service\Registry\ConfiguredRegisterable;

/**
 * Code to create and handle a REST API endpoint.
 */
interface Endpoint extends ConfiguredRegisterable {
	/**
	 * Get the configuration for this endpoint.
	 *
	 * @return EndpointConfiguration
	 */
	public static function getConfiguration(): EndpointConfiguration;

	/**
	 * Handle the endpoint.
	 *
	 * The path will have already been verified.
	 *
	 * @param ServerRequestInterface $request Incoming request object.
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface;
}
