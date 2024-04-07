<?php

namespace Smolblog\Core\ContentV1;

use Smolblog\Framework\Objects\SerializableKit;

/**
 * Content instantiated without a type.
 */
class GenericContent implements ContentType {
	use SerializableKit;

	/**
	 * Create the content
	 *
	 * @param string      $title           Content title.
	 * @param string      $body            HTML-formatted content body.
	 * @param string|null $originalTypeKey Tag with the original content's type.
	 */
	public function __construct(
		private string $title,
		private string $body,
		public readonly ?string $originalTypeKey = null
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

	/**
	 * Get the "key" for this content. It's content.
	 *
	 * Honestly, if this is being called, there's a problem.
	 *
	 * @return string
	 */
	public function getTypeKey(): string {
		return 'content';
	}
}
