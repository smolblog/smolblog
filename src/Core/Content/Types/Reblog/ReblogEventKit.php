<?php

namespace Smolblog\Core\Content\Types\Reblog;

/**
 * Common code for Reblog-related events.
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
	 * Get the HTML-formatted content of the status.
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
	 * @return string
	 */
	public function getMarkdown(): string {
		return $this->comment ?? '';
	}

	/**
	 * Store the rendered HTML.
	 *
	 * @param string $html Rendered HTML.
	 * @return void
	 */
	public function setHtml(string $html): void {
		$this->rendered = $html;
	}
}
