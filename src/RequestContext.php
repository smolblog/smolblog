<?php

namespace Smolblog\Core;

use Smolblog\Core\Models\User;
use Smolblog\Core\Models\Site;

// This sniff apparently does not support `readonly`.
//phpcs:disable Squiz.Commenting.VariableComment.Missing

/**
 * Context for the current web request, including authenticated user and
 * attached site if present.
 */
class RequestContext {
	/**
	 * Authenticated user making the request.
	 *
	 * @var User|null
	 */
	public readonly ?User $user;

	/**
	 * Site the request was made against.
	 *
	 * @var Site|null
	 */
	public readonly ?Site $site;

	/**
	 * Create the context with the given info.
	 *
	 * @param User|null $user Authenticated user making the request.
	 * @param Site|null $site Site request was made against.
	 */
	public function __construct(
		User $user = null,
		Site $site = null
	) {
		$this->user = $user;
		$this->site = $site;
	}
}
