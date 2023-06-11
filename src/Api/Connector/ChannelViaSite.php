<?php

namespace Smolblog\Api\Connector;

use Smolblog\Framework\Objects\Value;

/**
 * A Channel plus the permissions given on a site.
 */
class ChannelViaSite extends Channel {
	/**
	 * Construct the response
	 *
	 * @param boolean $canPull True if site is allowed to pull content from the channel.
	 * @param boolean $canPush True if site is allowed to push content to the channel.
	 * @param mixed   ...$args Channel() arguments.
	 */
	public function __construct(
		public readonly bool $canPull = false,
		public readonly bool $canPush = false,
		mixed ...$args,
	) {
		parent::__construct(...$args);
	}
}
