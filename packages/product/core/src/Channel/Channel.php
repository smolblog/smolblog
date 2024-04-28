<?php

namespace Smolblog\Core\Channel;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;
use Smolblog\Foundation\Value\Traits\{Entity, SerializableValue, SerializableValueKit};

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

	public function __construct(
		public string $provider,
		public string $providerKey,
		public ?Identifier $connectionId = null,
		public array $details = [],
	) {
	}

	public function getId(): Identifier {
		return self::buildId(provider: $this->provider, providerKey: $this->providerKey);
	}
}
