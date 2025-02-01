<?php

namespace Smolblog\Infrastructure\Endpoint;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Value\Http\HttpResponse;

/**
 * Shorthand for creating an Endpoint from a Command.
 *
 * Creates an Endpoint object based on conventions and annotations in the Command.
 */
abstract class CommandEndpoint implements Endpoint {
	public const string COMMAND = self::class;

	/**
	 * Create the Endpoint.
	 *
	 * @param CommandBus $bus CommandBus to execute the Command.
	 */
	public function __construct(private CommandBus $bus) {
	}

	/**
	 * Handle the web request.
	 *
	 * @throws \Exception Because it's not implemented yet.
	 *
	 * @param ServerRequestInterface $request Web request to answer.
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface {
		throw new \Exception('not yet');
		return new HttpResponse();
	}
}
