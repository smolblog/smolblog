<?php

namespace Smolblog\Core\Content\Markdown;

/**
 * For messages that have a Markdown field that needs to be rendered.
 */
interface NeedsMarkdownRendered {
	/**
	 * Get the Markdown text
	 *
	 * @return string
	 */
	public function getMarkdown(): string;

	/**
	 * Set the rendered HTML
	 *
	 * @param string $html Rendered HTML.
	 * @return void
	 */
	public function setHtml(string $html): void;
}
