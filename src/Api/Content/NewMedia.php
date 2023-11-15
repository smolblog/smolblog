<?php

namespace Smolblog\Api\Content;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\Verb;
use Smolblog\Core\Content\Media\HandleUploadedMedia;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\HttpResponse;

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
			queryVariables: [
				'title' => ParameterType::string(),
				'accessibilityText' => ParameterType::string(),
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
			file: $uploadedFiles[0],
			userId: $userId,
			siteId: $requestParams['site'],
			accessibilityText: $bodyParams['accessibilityText'],
			title: $bodyParams['title'],
		);

		return new HttpResponse(body: ['id' => $command->contentId]);
	}
}
