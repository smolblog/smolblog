<?php

namespace Smolblog\Core\Content;

use Smolblog\Framework\Objects\SerializableKit;

/**
 * Content instantiated without a type.
 */
class GenericContent implements ContentType {
	use SerializableKit;

	/**
	 * Create the content
	 *
	 * @param string      $title     Content title.
	 * @param string      $body      HTML-formatted content body.
	 * @param string|null $typeClass Tag with the original content's type.
	 */
	public function __construct(
		private string $title,
		private string $body,
		public readonly ?string $typeClass = null
	) {
	}

	/**
	 * Get the content title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/**
	 * Get the content body.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		return $this->body;
	}
}
