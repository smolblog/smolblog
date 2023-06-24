<?php

namespace Smolblog\Api\User;

use Smolblog\Api\AuthScope;
use Smolblog\Api\Endpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Core\User\UpdateProfile as UserUpdateProfile;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Endpoint to allow a user to update their profile.
 */
class UpdateMyProfile implements Endpoint {
	/**
	 * Get this endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/my/profile/update',
			verb: Verb::PUT,
			bodyClass: UpdateProfileBody::class,
			requiredScopes: [AuthScope::Profile],
		);
	}

	/**
	 * Construct the endpoint
	 *
	 * @param MessageBus $bus For sending messages.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @throws BadRequest No updated attribute was given.
	 *
	 * @param Identifier|null $userId User making the request.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Instance of UpdateProfileBody.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		try {
			$this->bus->dispatch(new UserUpdateProfile(
				userId: $userId,
				profileId: $userId,
				handle: $body->handle,
				displayName: $body->displayName,
				pronouns: $body->pronouns,
			));

			return new SuccessResponse();
		} catch (InvalidCommandParametersException $e) {
			throw new BadRequest(previous: $e);
		}
	}
}
