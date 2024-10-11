<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Traits\ArrayType;

/**
 * Store tags for a piece of content.
 *
 * Tags are versitile! They can be used for categorization, keywords, or just an aside comment.
 */
readonly class Tags extends ContentExtension {
	/**
	 * Create the extension
	 *
	 * @throws InvalidValueProperties If any non-string values are given.
	 *
	 * @param string[] $tags Tags as input by the user.
	 */
	public function __construct(#[ArrayType(ArrayType::TYPE_STRING)] public array $tags) {
		if (!empty(array_filter($tags, fn($tag) => !is_string($tag)))) {
			throw new InvalidValueProperties(message: 'All tags must be strings.', field: 'tags');
		}
	}
}
