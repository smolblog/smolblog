<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

/**
 * Common code for Reblog-related events.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
trait ReblogEventKit {
	/**
	 * Store the rendered HTML
	 *
	 * @var string
	 */
	private string $rendered;

	/**
	 * Get a title-appropriate truncation of the content.
	 *
	 * @return string
	 */
	public function getNewTitle(): ?string {
		return $this->info?->title ?? null;
	}

	/**
	 * Get the HTML-formatted content of the reblog.
	 *
	 * @return string
	 */
	public function getNewBody(): ?string {
		$embed = $this->info?->embed ?? "<p><a href=\"$this->url\">$this->url</a></p>";
		return $embed . "\n\n" . $this->rendered;
	}

	/**
	 * Get the unrendered Markdown.
	 *
	 * @return string[]
	 */
	public function getMarkdown(): array {
		return isset($this->comment) ? [$this->comment] : [];
	}

	/**
	 * Store the rendered HTML.
	 *
	 * @param string[] $html Rendered HTML.
	 * @return void
	 */
	public function setMarkdownHtml(array $html): void {
		$this->rendered = $html[0] ?? '';
	}

	/**
	 * Get the rendered HTML for the comment if it exists.
	 *
	 * @return string
	 */
	public function getCommentHtml(): string {
		return $this->rendered ?? '';
	}
}
