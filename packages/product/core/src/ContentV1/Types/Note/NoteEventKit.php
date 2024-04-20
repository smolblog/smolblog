<?php

namespace Smolblog\Core\ContentV1\Types\Note;

/**
 * Common code for Note-related events.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
trait NoteEventKit {
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
		return Note::truncateText($this->text);
	}

	/**
	 * Get the HTML-formatted content of the note.
	 *
	 * @return string
	 */
	public function getNewBody(): string {
		return $this->rendered;
	}

	/**
	 * Get the unrendered Markdown.
	 *
	 * @return string[]
	 */
	public function getMarkdown(): array {
		return [$this->text];
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
}
