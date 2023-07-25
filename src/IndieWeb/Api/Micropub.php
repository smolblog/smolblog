<?php

namespace Smolblog\IndieWeb\Api;

use DateTimeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\Verb;
use Smolblog\IndieWeb\Micropub\MicropubService;

/**
 * Micropub endpoint.
 *
 * @see https://indieweb.org/Micropub
 */
class Micropub implements Endpoint {
	/**
	 * Get endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/micropub',
			verb: [Verb::GET, Verb::POST],
			queryVariables: [
				'q' => ParameterType::required(ParameterType::string(pattern: '^(?:config|source|syndicate-to)$')),
				'url' => ParameterType::string(format: 'url'),
				'properties' => ParameterType::array(ParameterType::string()),
			],
			responseShape: ParameterType::object(value: ParameterType::string()),
			requiredScopes: [AuthScope::Identified],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MicropubService $micropub MicropubService which extends MicropubAdapter.
	 */
	public function __construct(
		private MicropubService $micropub,
	) {
	}

	/**
	 * Handle the endpoint.
	 *
	 * @param ServerRequestInterface $request Incoming request.
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface {
		wp_insert_post([
			'post_title' => 'Micropub endpoint ' . date(DateTimeInterface::COOKIE),
			'post_content' => print_r([
				'method' => $request->getMethod(),
				'query' => $request->getQueryParams(),
				'body' => $request->getBody()->getContents(),
			], true),
			'post_type' => 'log',
		], true);

		return $this->micropub->handleRequest($request);
	}
}
