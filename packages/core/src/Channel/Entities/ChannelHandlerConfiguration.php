<?php

namespace Smolblog\Core\Channel\Entities;

use Cavatappi\Foundation\Registry\RegisterableConfiguration;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;

/**
 * Configuration for a ChannelHandler.
 *
 * The canBeCanonical property indicates whether this channel can be used as a canonical store for content with stable
 * URLs. This should be something like a personal website and not something like a social media profile and definitely
 * not something like a content relay or machine-readable feed.
 */
readonly class ChannelHandlerConfiguration implements Value, RegisterableConfiguration {
	use ValueKit;

	/**
	 * Create the configuration.
	 *
	 * @param string  $key            Key for the handler.
	 * @param string  $displayName    User-friendly name for the Channel Handler.
	 * @param boolean $canBeCanonical True if this handler's channel can generate canonical URLs.
	 */
	public function __construct(
		public string $key,
		public string $displayName,
		public bool $canBeCanonical = false,
	) {}
}
