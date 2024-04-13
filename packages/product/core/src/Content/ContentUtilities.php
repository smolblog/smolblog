<?php

namespace Smolblog\Core\Content;

final class ContentUtilities {
	/**
	 * Truncated the given text to a given number of characters.
	 *
	 * @param string  $text  Text to truncate.
	 * @param integer $limit Line limit; default 100.
	 * @return string
	 */
	public static function truncateText(string $text, int $limit = 100): string {
		$truncated = substr($text, 0, strpos(wordwrap($text, $limit) . "\n", "\n"));
		if (strlen($text) > $limit) {
			$truncated .= '...';
		}
		return $truncated;
	}
}
