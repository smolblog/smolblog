<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Reflection\ListType;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value\ValueKit;
use Smolblog\Core\Content\Entities\ContentExtension;

/**
 * Store tags for a piece of content.
 *
 * Tags are versitile! They can be used for categorization, keywords, or just an aside comment.
 */
readonly class Tags implements ContentExtension, Validated {
	use ValueKit;

	/**
	 * Create the extension
	 *
	 * @throws InvalidValueProperties If any non-string values are given.
	 *
	 * @param string[] $tags Tags as input by the user.
	 */
	public function __construct(#[ListType('string')] public array $tags) {
		$this->validate();
	}

	public function validate(): void {
		if (!empty(array_filter($this->tags, fn($tag) => !is_string($tag)))) {
			throw new InvalidValueProperties(message: 'All tags must be strings.', field: 'tags');
		}
	}
}
