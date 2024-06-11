<?php

namespace Smolblog\Core\Channel;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;
use Smolblog\Foundation\Value\Traits\{Entity, SerializableValue, SerializableValueKit};

/**
 * A place where Content can be sent or retrieved from.
 *
 * This can be a social media account, a blog on a different system, or the website that comes with a site. Content is
 * "published" by pushing it to a Channel. The Channel's provider handles putting the Content into the Channel and
 * getting the resulting permalink.
 *
 * @codeCoverageIgnore
 */
readonly class Channel extends Value implements SerializableValue, Entity {
	use SerializableValueKit;

	public const NAMESPACE = '144af6d4-b4fb-4500-bb28-8e729cc7f585';

	/**
	 * Deterministicly build an ID from provider and key.
	 *
	 * The two values are concatenated and used for the Channel ID. This way, the 'activitypub' channel '@bob@smol.blog'
	 * will always have the ID '69d5a8c3-d461-539c-baf1-daeea2880464'.
	 *
	 * @param string $provider    Key for the provider used to power this Channel.
	 * @param string $providerKey Unique value for this channel for this provider (username, URL, etc).
	 * @return Identifier
	 */
	public static function buildId(string $provider, string $providerKey): Identifier {
		return new NamedIdentifier(namespace: self::NAMESPACE, name: "$provider|$providerKey");
	}

	/**
	 * Construct the channel.
	 *
	 * @param string          $provider     Key for the provider used to power this Channel.
	 * @param string          $providerKey  Unique value for this channel for this provider (username, URL, etc).
	 * @param Identifier|null $connectionId Connection to use to authenticate to this Channel.
	 * @param array           $details      Provider-required details for this Channel.
	 */
	public function __construct(
		public string $provider,
		public string $providerKey,
		public ?Identifier $connectionId = null,
		public array $details = [],
	) {
	}

	/**
	 * Get the ID for this Channel.
	 *
	 * @return Identifier
	 */
	public function getId(): Identifier {
		return self::buildId(provider: $this->provider, providerKey: $this->providerKey);
	}
}
