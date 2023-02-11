<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Core\Content\ContentExtension;
use Smolblog\Framework\Objects\SerializableKit;

/**
 * Tags! They exist!
 *
 * Tags are gathered in full unicode text, but they are stripped to letters and numbers only for the purposes of
 * comparison. The idea is that "someThing" and "some thing" will be considered the same tag. This will likely
 * change as things get more mature, which is why the _events_ only store the full unicode text.
 */
class Tags implements ContentExtension {
	use SerializableKit;

	/**
	 * Transform the tag text into a normalized format.
	 *
	 * @param string $text Original tag text.
	 * @return string
	 */
	public static function normalizeTagText(string $text): string {
		return strtolower(preg_replace('/[^\w\d]/', '', $text));
	}

	/**
	 * Store the tags on the content.
	 *
	 * @var Tag[]
	 */
	public readonly array $tags;

	/**
	 * Construct the extension.
	 *
	 * @param Tag[] $tags Tags on the content.
	 */
	public function __construct(array $tags) {
		$this->setTags($tags);
	}

	/**
	 * Set the tags for this content, replacing any that may exist.
	 *
	 * @param Tag[] $tags Tags on the content.
	 * @return void
	 */
	public function setTags(array $tags) {
		$this->tags = $tags;
	}
}
