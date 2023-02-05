<?php

namespace Smolblog\Core\Content\Types\Status;

/**
 * Common code for Status-related events.
 */
trait StatusEventKit {
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
	public function getNewTitle(): string {
		return Status::truncateText($this->text);
	}

	/**
	 * Get the HTML-formatted content of the status.
	 *
	 * @return string
	 */
	public function getNewBody(): string {
		return $this->rendered;
	}

	/**
	 * Get the unrendered Markdown.
	 *
	 * @return string
	 */
	public function getMarkdown(): string {
		return $this->text;
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
