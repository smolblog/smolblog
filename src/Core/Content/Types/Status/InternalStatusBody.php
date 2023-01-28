<?php

namespace Smolblog\Core\Content\Types\Status;

/**
 * For internal use only.
 *
 * Internal representation of a status body. Allows logic sharing between the content object and necessary events.
 */
class InternalStatusBody {
	/**
	 * Construct the body
	 *
	 * @param string $text Markdown-formatted text.
	 */
	public function __construct(public readonly string $text) {
	}

	/**
	 * Get the text truncated to a given number of characters.
	 *
	 * @param integer $limit Line limit.
	 * @return string
	 */
	public function getTruncated(int $limit): string {
		$truncated = substr($this->text, 0, strpos(wordwrap($this->text, $limit) . "\n", "\n"));
		if (strlen($this->text) > $limit) {
			$truncated .= '...';
		}
		return $truncated;
	}
}
