<?php

namespace Smolblog\Core\Content;

/**
 * Content instantiated without a type.
 */
class GenericContent extends Content {
	/**
	 * Create the content
	 *
	 * @param string $title    Content title.
	 * @param string $body     HTML-formatted content body.
	 * @param mixed  ...$props Content properties.
	 */
	public function __construct(private string $title, private string $body, mixed ...$props) {
		parent::__construct(...$props);
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