<?php

namespace Smolblog\Core\Content\Extensions\SyndicationLinks;

use Smolblog\Core\Content\ContentExtension;

/**
 * Indicate other places around the internet this content can be found.
 *
 * The post exists on your blog. Links have been posted to Twitter and Facebook, and a copy was posted to Tumblr.
 * Links to all of *those* posts are SyndicationLinks.
 */
class SyndicationLinks implements ContentExtension {
	/**
	 * Store the list of links.
	 *
	 * @var array
	 */
	public readonly array $links;

	/**
	 * Construct the extension.
	 *
	 * @param SyndicationLink[] $links SyndicationLinks on the content.
	 */
	public function __construct(array $links) {
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
		return['link' => array_map(fn($link) => $link->toArray(), $this->links)];
	}

	/**
	 * Deserialize this extension.
	 *
	 * @param array $data Serialized extension.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		return new static(links: array_map(fn($link) => SyndicationLink::fromArray($link), $data['links']));
	}
}
