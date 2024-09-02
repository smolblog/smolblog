<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use Smolblog\Core\Content\ContentExtension;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicate other places around the internet this content can be found or should be posted.
 *
 * The post exists on your blog. Links have been posted to Twitter and Facebook, and a copy was posted to Tumblr.
 * Links to all of *those* posts are SyndicationLinks.
 *
 * You're writing the post. You want it to post to Tumblr and a newsletter. Those channels are listed here.
 */
class Syndication implements ContentExtension {
	/**
	 * @deprecated 0.1 This is a hack to reduce the footprint of a code change.
	 */
	public const KEY = 'syndication';

	/**
	 * Store the list of links.
	 *
	 * @var SyndicationLink[]
	 */
	public readonly array $links;

	/**
	 * Store the channels this content should syndicate to upon publish.
	 *
	 * @var Identifier[]
	 */
	public readonly array $channels;

	/**
	 * Construct the extension.
	 *
	 * @param SyndicationLink[] $links    SyndicationLinks on the content.
	 * @param Identifier[]      $channels IDs of channels to syndicate to.
	 */
	public function __construct(array $links, array $channels) {
		$this->channels = $channels;
		$this->setLinks($links);
	}

	/**
	 * Set the links for this content, replacing any that may exist.
	 *
	 * @param SyndicationLink[] $links SyndicationLinks on the content.
	 * @return void
	 */
	public function setLinks(array $links) {
		$this->links = $links;
	}

	/**
	 * Serialize this extension.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return[
			'links' => array_map(fn($link) => $link->toArray(), $this->links),
			'channels' => array_map(fn($channel) => $channel->toString(), $this->channels),
		];
	}

	/**
	 * Deserialize this extension.
	 *
	 * @param array $data Serialized extension.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		return new static(
			links: array_map(fn($link) => SyndicationLink::fromArray($link), $data['links'] ?? []),
			channels: array_map(fn($channel) => Identifier::fromString($channel), $data['channels'] ?? []),
		);
	}
}
