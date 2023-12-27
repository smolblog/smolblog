<?php

namespace Smolblog\ActivityPub\Follow;

use Exception;
use Psr\Http\Client\ClientInterface;
use Smolblog\ActivityPhp\Type\Extended\Activity\Create;
use Smolblog\ActivityPub\ActivityTypesConverter;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Federation\FollowerProvider;
use Smolblog\Core\Site\GetSiteKeypair;
use Smolblog\Core\Site\SiteById;
use Smolblog\Core\User\User;
use Smolblog\Framework\Infrastructure\HttpSigner;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;

/**
 * Service that handles posting content to ActivityPub.
 */
class ActivityPubFollowerProvider implements FollowerProvider {
	public const SLUG = 'activitypub';

	/**
	 * Get the slug for this provider.
	 *
	 * @return string
	 */
	public static function getSlug(): string {
		return self::SLUG;
	}

	/**
	 * Construct the service.
	 *
	 * @param MessageBus             $bus     For sending internal messages.
	 * @param ClientInterface        $fetcher For sending content.
	 * @param ApiEnvironment         $env     For creating links.
	 * @param ActivityTypesConverter $at      For creating ActivityTypes objects.
	 * @param HttpSigner             $signer  For signing HTTP requests.
	 */
	public function __construct(
		private MessageBus $bus,
		private ClientInterface $fetcher,
		private ApiEnvironment $env,
		private ActivityTypesConverter $at,
		private HttpSigner $signer,
	) {
	}

	/**
	 * Post the given content to the given ActivityPub followers.
	 *
	 * @throws Exception When the remote server throws an error.
	 *
	 * @param Content $content   Content being created.
	 * @param array   $followers Followers interested in said content.
	 * @return void
	 */
	public function sendContentToFollowers(Content $content, array $followers): void {
		$site = $this->bus->fetch(new SiteById($content->siteId));
		$eventId = new DateIdentifier();

		$apMessage = new Create();
		$apMessage->id = $this->env->getApiUrl("/site/$content->siteId/activitypub/outbox/$eventId");
		$apMessage->actor = $this->env->getApiUrl("/site/$content->siteId/activitypub/actor");
		$apMessage->object = $this->at->activityObjectFromContent(content: $content, site: $site);

		$inboxes = array_values(array_unique(array_map(
			fn($follower) => $follower->details['sharedInbox'] ?? $follower->details['inbox'],
			$followers
		)));

		foreach ($inboxes as $inbox) {
			$request = new HttpRequest(verb: HttpVerb::POST, url: $inbox, body: $apMessage->toArray());

			$keypair = $this->bus->fetch(new GetSiteKeypair(siteId: $site->id, userId: User::internalSystemUser()->id));
			$request = $this->signer->sign(
				request: $request,
				keyId: "$apMessage->actor#publicKey",
				keyPem: $keypair->privateKey,
			);

			$acceptResponse = $this->fetcher->sendRequest($request);
			$resCode = $acceptResponse->getStatusCode();
			if ($resCode >= 300 || $resCode < 200) {
				throw new Exception('Error from federated server: ' . $acceptResponse->getBody()->getContents());
			}
		}
	}
}
