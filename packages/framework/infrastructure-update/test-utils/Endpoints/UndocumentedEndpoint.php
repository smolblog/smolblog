<?php

namespace Smolblog\Infrastructure\Test\Endpoints;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\Foundation\Value\Http\HttpResponse;
use Smolblog\Infrastructure\Endpoint\Endpoint;
use Smolblog\Infrastructure\Endpoint\EndpointConfiguration;

class UndocumentedEndpoint implements Endpoint {
	public static function getConfiguration(): EndpointConfiguration {
		return new EndpointConfiguration(
			route: '/test/undocumented',
			key: 'UndocumentedEndpoint',
		);
	}

	public function handle(ServerRequestInterface $request): ResponseInterface {
		return new HttpResponse(code: 204);
	}
}
