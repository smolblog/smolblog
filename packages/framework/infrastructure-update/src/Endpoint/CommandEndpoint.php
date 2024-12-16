<?php

namespace Smolblog\Infrastructure\Endpoint;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Value\Http\HttpResponse;

abstract class CommandEndpoint implements Endpoint {
	use ErrorResponseKit;

	public const string COMMAND = self::class;

	public function __construct(private CommandBus $bus) {
	}

	public function handle(ServerRequestInterface $request): ResponseInterface {
		throw new \Exception('not yet');
		return new HttpResponse();
	}
}
