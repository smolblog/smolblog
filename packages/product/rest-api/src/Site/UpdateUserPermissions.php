<?php

namespace Smolblog\Api\Site;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\Site\LinkSiteAndUser;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Set permissions for a user on a site.
 */
class UpdateUserPermissions extends BasicEndpoint {
	/**
	 * Get endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/users/set',
			verb: Verb::POST,
			pathVariables: [
				'site' => ParameterType::identifier(),
			],
			bodyClass: UserPermissionPayload::class,
			requiredScopes: [AuthScope::Admin],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus For dispatching commands.
	 */
	public function __construct(private MessageBus $bus) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId User making the request.
	 * @param array|null      $params Expects site parameter.
	 * @param object|null     $body   Instance of UserPermissionPayload.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		$this->bus->dispatch(new LinkSiteAndUser(
			siteId: $params['site'],
			linkedUserId: $body->userId,
			commandUserId: $userId,
			isAuthor: $body->isAuthor,
			isAdmin: $body->isAdmin,
		));

		return new SuccessResponse();
	}
}
