<?php

namespace Smolblog\Core\Connector;

use Smolblog\Framework\Entity;

/**
 * Represents a single content channel, such as a blog, RSS feed, or social media profile. Since some social media
 * providers allow multiple profiles/blogs/channels/etc. per account, this is its own Entity.
 */
class Channel extends Entity {
	/**
	 * Consistently build a unique identifier out of the provider and key.
	 *
	 * @param string $connectionId ID for the Connecton this is tied to.
	 * @param string $channelKey   Unique identifier for this channel within this connection.
	 * @return string ID constructed from connection and key.
	 */
	public static function buildId(string $connectionId, string $channelKey): string {
		return "$connectionId|$channelKey";
	}

	/**
	 * Construct the channel
	 *
	 * @param string $connectionId ID of the Connection this Channel belongs to.
	 * @param string $channelKey   Unique identifier for this Channel within the Connection.
	 * @param string $displayName  Recognizable name for the channel.
	 * @param array  $details      Any specific information needed to use the channel.
	 */
	public function __construct(
		public readonly string $connectionId,
		public readonly string $channelKey,
		public readonly string $displayName,
		public readonly array $details,
	) {
		parent::__construct(id: self::buildId(connectionId: $connectionId, channelKey: $channelKey));
	}
}
