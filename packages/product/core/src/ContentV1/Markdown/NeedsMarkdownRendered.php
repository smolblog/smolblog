<?php

namespace Smolblog\Core\ContentV1\Markdown;

/**
 * For messages that have Markdown fields that needs to be rendered.
 *
 * @deprecated Migrate to Smolblog\Core\Content
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
