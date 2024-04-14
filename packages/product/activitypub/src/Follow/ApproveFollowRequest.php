<?php

namespace Smolblog\ActivityPub\Follow;

use Smolblog\Core\Site\Site;
use Smolblog\Framework\ActivityPub\Objects\Actor;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Approve the given Follow request.
 *
 * All requests are currently auto-approved; this command exists so the response can be sent async.
 */
readonly class ApproveFollowRequest extends Command implements AuthorizableMessage {
	/**
	 * Construct the command.
	 *
	 * Either $siteId or $site is required.
	 *
	 * @throws InvalidCommandParametersException When a site or site ID is not provided.
	 *
	 * @param Identifier      $userId  User approving the request.
	 * @param Follow          $request Request being approved.
	 * @param Actor           $actor   Actor giving the request.
	 * @param Identifier|null $siteId  ID of site being followed.
	 * @param Site|null       $site    Full object of site being followed.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Follow $request,
		public readonly Actor $actor,
		public readonly ?Identifier $siteId = null,
		public readonly ?Site $site = null,
	) {
		if (!isset($siteId) && !isset($site)) {
			throw new InvalidCommandParametersException(
				command: $this,
				message: 'Either a Site or a Site ID must be provided.',
			);
		}
	}

	/**
	 * Find out if the user on this query is allowed to approve requests.
	 *
	 * @return UserCanApproveFollowers
	 */
	public function getAuthorizationQuery(): UserCanApproveFollowers {
		return new UserCanApproveFollowers(
			siteId: $this->siteId ?? $this->site->id,
			userId: $this->userId,
		);
	}
}
