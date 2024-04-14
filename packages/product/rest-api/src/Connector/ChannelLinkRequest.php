<?php

namespace Smolblog\Api\Connector;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Request body for ChannelLink
 */
readonly class ChannelLinkRequest extends Value implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Construct the request.
	 *
	 * @param Identifier $channelId ID of channel to link.
	 * @param Identifier $siteId    ID of site to link.
	 * @param boolean    $push      True if site should be able to push content.
	 * @param boolean    $pull      True if site should be able to pull content.
	 */
	public function __construct(
		public readonly Identifier $channelId,
		public readonly Identifier $siteId,
		public readonly ?bool $push = null,
		public readonly ?bool $pull = null,
	) {
	}

	/**
	 * Deserialize from an array.
	 *
	 * @param array $data Serialized data.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		$params = [
			...$data,
			'channelId' => Identifier::fromString($data['channelId']),
			'siteId' => Identifier::fromString($data['siteId']),
		];

		return new static(...$params);
	}
}
