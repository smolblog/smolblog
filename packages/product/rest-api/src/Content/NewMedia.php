<?php

namespace Smolblog\Api\Content;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\Verb;
use Smolblog\Core\ContentV1\Media\HandleUploadedMedia;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Framework\Objects\HttpResponse;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Endpoint to create a new media object.
 */
class NewMedia implements Endpoint {
	/**
	 * Get the endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/media/new',
			verb: Verb::POST,
			pathVariables: [
				'site' => ParameterType::identifier(),
			],
			responseShape: ParameterType::object(id: ParameterType::string()),
			requiredScopes: [AuthScope::Create],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus MessageBus for sending the command.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Handle the endpoint.
	 *
	 * @param ServerRequestInterface $request Incoming request.
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface {
		$uploadedFiles = $request->getUploadedFiles();
		if (empty($uploadedFiles)) {
			return new HttpResponse(code: 400, body: ['code' => 400, 'error' => 'No media was provided.']);
		}

		$userId = $request->getAttribute('smolblogUserId');
		$requestParams = array_merge($request->getQueryParams(), $request->getAttribute('smolblogPathVars', []));
		$bodyParams = $request->getParsedBody();

		$command = new HandleUploadedMedia(
			file: $uploadedFiles['file'],
			userId: $userId,
			siteId: Identifier::fromString($requestParams['site']),
			accessibilityText: $bodyParams['accessibilityText'],
			title: $bodyParams['title'],
		);
		$this->bus->dispatch($command);

		return new HttpResponse(body: ['id' => $command->contentId]);
	}
}
