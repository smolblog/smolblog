<?php

namespace Smolblog\Infrastructure\Test\Endpoints;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\Foundation\Value\Http\HttpResponse;
use Smolblog\Foundation\Value\Http\HttpVerb;
use Smolblog\Infrastructure\Endpoint\EndpointConfiguration;
use Smolblog\Infrastructure\OpenApi\OpenApiDocumentedEndpoint;
use Smolblog\Infrastructure\OpenApi\OpenApiEndpointSpec;

class CustomDocumentedEndpoint implements OpenApiDocumentedEndpoint {
	public static function getConfiguration(): EndpointConfiguration {
		return new EndpointConfiguration(
			route: '/test/custom-documented',
			verb: HttpVerb::POST,
			auth: false,
		);
	}

	public static function getOpenApiSpec(): OpenApiEndpointSpec {
		return new OpenApiEndpointSpec(
			operation: []
		);
	}

	public function handle(ServerRequestInterface $request): ResponseInterface {
		return new HttpResponse(code: 204);
	}
}
