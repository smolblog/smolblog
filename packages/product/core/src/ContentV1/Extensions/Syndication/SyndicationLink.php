<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Store information about a link to a piece of content available elsewhere.
 */
class SyndicationLink extends Value {
	/**
	 * Construct the link.
	 *
	 * @param string          $url       URL to the external content.
	 * @param Identifier|null $channelId Optional ID of the channel used to syndicate.
	 */
	public function __construct(
		public readonly string $url,
		public readonly ?Identifier $channelId = null,
	) {
	}

	/**
	 * Serialize this link.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return array_filter([
			'url' => $this->url,
			'channelId' => $this->channelId?->toString(),
		]);
	}

	/**
	 * Deserialize a link from an array.
	 *
	 * @param array $data Serialized link.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		return new static(url: $data['url'], channelId: self::safeDeserializeIdentifier($data['channelId'] ?? ''));
	}
}
