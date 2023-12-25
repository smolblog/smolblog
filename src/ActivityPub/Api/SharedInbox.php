<?php

namespace Smolblog\ActivityPub\Api;

use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Smolblog\ActivityPhp\Type\Extended\Activity\Follow;
use Smolblog\ActivityPub\InboxService;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\ParameterType;
use Smolblog\Api\SuccessResponse;
use Smolblog\Api\Verb;
use Smolblog\Framework\Objects\Identifier;

/**
 * Server-wide ActivityPub Inbox endpoint.
 */
class SharedInbox extends BasicEndpoint {
	/**
	 * Get endpoint configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/activitypub/inbox',
			verb: Verb::POST,
			bodyClass: ActivityObject::class,
			requiredScopes: [],
		);
	}

	/**
	 * Create the endpoint.
	 *
	 * @param InboxService    $service Service to handle the request.
	 * @param ApiEnvironment  $env     Environment for the request.
	 * @param LoggerInterface $log     Logger to log requests.
	 */
	public function __construct(
		private InboxService $service,
		private ApiEnvironment $env,
		private LoggerInterface $log,
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @param Identifier|null $userId User making the request. Ignored.
	 * @param array|null      $params Ignored.
	 * @param object|null     $body   Some ActivityPub object expected.
	 * @return SuccessResponse
	 */
	public function run(?Identifier $userId, ?array $params, ?object $body): SuccessResponse {
		$this->log->debug(
			message: 'ActivityPub Site Inbox' . date(DateTimeInterface::COOKIE),
			context: [
				'params' => $params,
				'body' => $body,
			],
		);

		switch (get_class($body)) {
			case Follow::class:
				$this->service->handleFollow(request: $body, siteId: $this->getSiteIdFromProperty($body->object));
				break;

			default:
				break;
		}

		return new SuccessResponse();
	}

	/**
	 * Turn a Smolblog actor URL or object into a site ID.
	 *
	 * @param string|object $prop Actor object or url string.
	 * @return Identifier
	 */
	private function getSiteIdFromProperty(string|object $prop): Identifier {
		$actorUrl = $prop;
		if (is_object($prop) && is_string($prop->id)) {
			$actorUrl = $prop->id;
		}

		$matches = [];
		$pattern = '/\/site\/([a-fA-F0-9\-]{36})\/activitypub\/actor/';
		preg_match($pattern, $actorUrl, $matches);

		return Identifier::fromString($matches[1]);
	}
}
