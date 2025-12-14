<?php

namespace Smolblog\Core\Channel\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\ServiceConfiguration;
use Smolblog\Foundation\Value\Traits\ServiceConfigurationKit;

/**
 * Configuration for a ChannelHandler.
 *
 * The canBeCanonical property indicates whether this channel can be used as a canonical store for content with stable
 * URLs. This should be something like a personal website and not something like a social media profile and definitely
 * not something like a content relay or machine-readable feed.
 */
readonly class ChannelHandlerConfiguration extends Value implements ServiceConfiguration {
	use ServiceConfigurationKit;

	/**
	 * Create the configuration.
	 *
	 * @param string  $key            Key for the handler.
	 * @param string  $displayName    User-friendly name for the Channel Handler.
	 * @param boolean $canBeCanonical True if this handler's channel can generate canonical URLs.
	 */
	public function __construct(
		string $key,
		public string $displayName,
		public bool $canBeCanonical = false,
	) {
		$this->key = $key;
	}
}
