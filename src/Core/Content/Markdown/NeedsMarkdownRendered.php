<?php

namespace Smolblog\Core\Content\Markdown;

/**
 * For messages that have Markdown fields that needs to be rendered.
 */
interface NeedsMarkdownRendered {
	/**
	 * Get the Markdown text
	 *
	 * @return string[]
	 */
	public function getMarkdown(): array;

	/**
	 * Set the rendered HTML
	 *
	 * @param string[] $html Rendered HTML.
	 * @return void
	 */
	public function setMarkdownHtml(array $html): void;
}
