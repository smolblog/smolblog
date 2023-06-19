<?php

namespace Smolblog\ActivityPub\Follow;

use Psr\Http\Client\ClientInterface;
use Smolblog\ActivityPhp\Type\Extended\Activity\Accept;
use Smolblog\Framework\Infrastructure\HttpSigner;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Objects\HttpRequest;
use Smolblog\Framework\Objects\HttpVerb;

/**
 * Service for handling follow-related commands.
 */
class FollowService implements Listener {
	public function __construct(
		private ClientInterface $fetcher,
		private HttpSigner $signer,
	)
	{

	}

	public function onApproveFollowRequest(ApproveFollowRequest $command): void {
		$body = new Accept();
		// set body props

		$this->fetcher->sendRequest(new HttpRequest(
			verb: HttpVerb::POST,
			url: $command->request->actor->inbox,
			body: $body->toArray(),
		));
	}
}
