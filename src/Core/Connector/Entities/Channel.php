<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Entity;
use Smolblog\Framework\Identifier;

/**
 * Represents a single content channel, such as a blog, RSS feed, or social media profile. Since some social media
 * providers allow multiple profiles/blogs/channels/etc. per account, this is its own Entity.
 */
class Channel extends Entity {
	public const NAMESPACE = '144af6d4-b4fb-4500-bb28-8e729cc7f585';

	/**
	 * Consistently build a unique identifier out of the provider and key.
	 *
	 * @param Identifier $connectionId ID for the Connecton this is tied to.
	 * @param string     $channelKey   Unique identifier for this channel within this connection.
	 * @return Identifier ID constructed from connection and key.
	 */
	public static function buildId(Identifier $connectionId, string $channelKey): Identifier {
		return Identifier::createFromName(namespace: self::NAMESPACE, name: "$connectionId|$channelKey");
	}

	/**
	 * Construct the channel
	 *
	 * @param Identifier $connectionId ID of the Connection this Channel belongs to.
	 * @param string     $channelKey   Unique identifier for this Channel within the Connection.
	 * @param string     $displayName  Recognizable name for the channel.
	 * @param array      $details      Any specific information needed to use the channel.
	 */
	public function __construct(
		public readonly Identifier $connectionId,
		public readonly string $channelKey,
		public readonly string $displayName,
		public readonly array $details,
	) {
		parent::__construct(id: self::buildId(connectionId: $connectionId, channelKey: $channelKey));
	}
}
